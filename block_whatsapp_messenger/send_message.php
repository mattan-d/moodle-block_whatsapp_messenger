<?php
// This file is part of Moodle - http://moodle.org/

require_once('../../config.php');

require_login();

$courseid = required_param('courseid', PARAM_INT);
$studentids = required_param('students', PARAM_TEXT); // Comma-separated IDs or 'all'
$message = required_param('message', PARAM_TEXT);

$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
$context = context_course::instance($courseid);

require_capability('block/whatsapp_messenger:sendmessage', $context);

// Get WhatsApp configuration
$accesstoken = get_config('block_whatsapp_messenger', 'whatsapp_access_token');
$phonenumberid = get_config('block_whatsapp_messenger', 'whatsapp_phone_number_id');
$templatename = get_config('block_whatsapp_messenger', 'template_name');
$templatelanguage = get_config('block_whatsapp_messenger', 'template_language');
$usetemplate = get_config('block_whatsapp_messenger', 'use_template');

if (empty($accesstoken) || empty($phonenumberid)) {
    echo json_encode([
        'success' => false,
        'error' => get_string('notconfigured', 'block_whatsapp_messenger')
    ]);
    exit;
}

// Get students to send to
if ($studentids === 'all') {
    $students = get_enrolled_students_with_phones($courseid);
} else {
    $ids = explode(',', $studentids);
    $students = [];
    foreach ($ids as $id) {
        $user = $DB->get_record('user', ['id' => intval($id)], 'id,firstname,lastname,phone1,phone2');
        if ($user && !empty($user->phone1)) {
            $students[] = $user;
        }
    }
}

if (empty($students)) {
    echo json_encode([
        'success' => false,
        'error' => get_string('selectstudentserror', 'block_whatsapp_messenger')
    ]);
    exit;
}

$apiurl = "https://graph.facebook.com/v19.0/{$phonenumberid}/messages";
$successcount = 0;
$failcount = 0;
$errors = [];

foreach ($students as $student) {
    $phonenumber = format_phone_number($student->phone1);
    
    if (empty($phonenumber)) {
        $failcount++;
        $errors[] = get_string('invalidphone', 'block_whatsapp_messenger', fullname($student));
        continue;
    }

    // Build payload
    if ($usetemplate) {
        // Use template format
        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $phonenumber,
            'type' => 'template',
            'template' => [
                'name' => $templatename,
                'language' => ['code' => $templatelanguage],
                'components' => [
                    [
                        'type' => 'body',
                        'parameters' => [
                            ['type' => 'text', 'text' => $student->firstname],
                            ['type' => 'text', 'text' => sanitize_message($message)]
                        ]
                    ]
                ]
            ]
        ];
    } else {
        // Use text message (may require approved template in production)
        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $phonenumber,
            'type' => 'text',
            'text' => [
                'body' => sanitize_message($message)
            ]
        ];
    }

    // Send via WhatsApp API
    $ch = curl_init($apiurl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $accesstoken,
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $responsedata = json_decode($response, true);

    // Log to database
    $record = new stdClass();
    $record->courseid = $courseid;
    $record->senderid = $USER->id;
    $record->recipientid = $student->id;
    $record->message = $message;
    $record->phonenumber = $phonenumber;
    $record->timecreated = time();

    if ($httpcode == 200 && isset($responsedata['messages'][0]['id'])) {
        $record->status = 'sent';
        $record->whatsapp_message_id = $responsedata['messages'][0]['id'];
        $successcount++;
    } else {
        $record->status = 'failed';
        $record->error_message = isset($responsedata['error']['message']) 
            ? $responsedata['error']['message'] 
            : 'Unknown error';
        $failcount++;
        $errors[] = fullname($student) . ': ' . $record->error_message;
    }

    $DB->insert_record('block_whatsapp_messages', $record);
}

// Return response
echo json_encode([
    'success' => true,
    'sent' => $successcount,
    'failed' => $failcount,
    'errors' => $errors
]);

/**
 * Get enrolled students with phone numbers
 */
function get_enrolled_students_with_phones($courseid) {
    global $DB;
    
    $sql = "SELECT u.id, u.firstname, u.lastname, u.phone1, u.phone2
            FROM {user} u
            JOIN {user_enrolments} ue ON ue.userid = u.id
            JOIN {enrol} e ON e.id = ue.enrolid
            JOIN {role_assignments} ra ON ra.userid = u.id
            JOIN {context} ctx ON ctx.id = ra.contextid
            JOIN {role} r ON r.id = ra.roleid
            WHERE e.courseid = :courseid
            AND ctx.contextlevel = :contextlevel
            AND ctx.instanceid = :contextid
            AND r.archetype = 'student'
            AND u.deleted = 0
            AND u.suspended = 0
            AND (u.phone1 IS NOT NULL AND u.phone1 != '')
            GROUP BY u.id, u.firstname, u.lastname, u.phone1, u.phone2";

    return $DB->get_records_sql($sql, [
        'courseid' => $courseid,
        'contextlevel' => CONTEXT_COURSE,
        'contextid' => $courseid
    ]);
}

/**
 * Format phone number for WhatsApp (must include country code)
 */
function format_phone_number($phone) {
    // Remove all non-numeric characters
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // Ensure it has country code
    if (strlen($phone) < 10) {
        return '';
    }
    
    return $phone;
}

/**
 * Sanitize message for WhatsApp
 */
function sanitize_message($text) {
    return preg_replace('/[\r\n\t]+/', ' ', trim($text));
}
