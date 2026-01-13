<?php
// This file is part of Moodle - http://moodle.org/

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/filelib.php');

$courseid = required_param('courseid', PARAM_INT);
$recipient = required_param('recipient', PARAM_TEXT);
$message = required_param('message', PARAM_TEXT);

require_login();
require_sesskey();

$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
$context = context_course::instance($courseid);

require_capability('block/whatsapp_messenger:sendmessage', $context);

$accesstoken = get_config('block_whatsapp_messenger', 'accesstoken');
$phonenumberid = get_config('block_whatsapp_messenger', 'phonenumberid');
$apiversion = get_config('block_whatsapp_messenger', 'apiversion') ?: 'v17.0';
$templatename = get_config('block_whatsapp_messenger', 'templatename');
$templatelang = get_config('block_whatsapp_messenger', 'templatelang') ?: 'en';

if (empty($accesstoken) || empty($phonenumberid)) {
    echo json_encode(['success' => false, 'error' => get_string('notconfigured', 'block_whatsapp_messenger')]);
    exit;
}

$recipients = [];

// Get recipient(s)
if ($recipient === 'all') {
    $sql = "SELECT u.id, u.firstname, u.lastname, u.phone1, u.phone2
            FROM {user} u
            JOIN {user_enrolments} ue ON u.id = ue.userid
            JOIN {enrol} e ON ue.enrolid = e.id
            WHERE e.courseid = :courseid
            AND u.deleted = 0
            AND u.suspended = 0
            AND (u.phone1 IS NOT NULL AND u.phone1 != '')";
    $recipients = $DB->get_records_sql($sql, ['courseid' => $courseid]);
} else {
    $user = $DB->get_record('user', ['id' => $recipient], '*', MUST_EXIST);
    if (!empty($user->phone1) || !empty($user->phone2)) {
        $recipients = [$user];
    }
}

if (empty($recipients)) {
    echo json_encode(['success' => false, 'error' => get_string('norecipients', 'block_whatsapp_messenger')]);
    exit;
}

// Send messages
$success_count = 0;
$failed_count = 0;
$api_url = "https://graph.facebook.com/{$apiversion}/{$phonenumberid}/messages";

foreach ($recipients as $user) {
    $phone = !empty($user->phone1) ? $user->phone1 : $user->phone2;
    
    // Clean phone number (remove spaces, dashes, etc.)
    $phone = preg_replace('/[^0-9+]/', '', $phone);
    
    if (!empty($templatename)) {
        // Use template message
        $data = [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'template',
            'template' => [
                'name' => $templatename,
                'language' => [
                    'code' => $templatelang
                ],
                'components' => [
                    [
                        'type' => 'body',
                        'parameters' => [
                            [
                                'type' => 'text',
                                'text' => $message
                            ]
                        ]
                    ]
                ]
            ]
        ];
    } else {
        // Use text message
        $data = [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'text',
            'text' => [
                'body' => $message
            ]
        ];
    }
    
    // Send request using Moodle's curl wrapper
    $curl = new curl();
    $curl->setHeader([
        'Authorization: Bearer ' . $accesstoken,
        'Content-Type: application/json'
    ]);
    
    try {
        $response = $curl->post($api_url, json_encode($data));
        $result = json_decode($response, true);
        
        if (isset($result['messages'])) {
            $success_count++;
            
            // Log successful message
            $log = new stdClass();
            $log->courseid = $courseid;
            $log->userid = $user->id;
            $log->phone = $phone;
            $log->message = $message;
            $log->status = 'sent';
            $log->messageid = $result['messages'][0]['id'] ?? '';
            $log->timecreated = time();
            $log->senderid = $USER->id;
            
            $DB->insert_record('block_whatsapp_messenger_log', $log);
        } else {
            $failed_count++;
            
            // Log failed message
            $log = new stdClass();
            $log->courseid = $courseid;
            $log->userid = $user->id;
            $log->phone = $phone;
            $log->message = $message;
            $log->status = 'failed';
            $log->error = json_encode($result);
            $log->timecreated = time();
            $log->senderid = $USER->id;
            
            $DB->insert_record('block_whatsapp_messenger_log', $log);
        }
    } catch (Exception $e) {
        $failed_count++;
        
        // Log error
        $log = new stdClass();
        $log->courseid = $courseid;
        $log->userid = $user->id;
        $log->phone = $phone;
        $log->message = $message;
        $log->status = 'failed';
        $log->error = $e->getMessage();
        $log->timecreated = time();
        $log->senderid = $USER->id;
        
        $DB->insert_record('block_whatsapp_messenger_log', $log);
    }
}

echo json_encode([
    'success' => true,
    'sent' => $success_count,
    'failed' => $failed_count,
    'total' => count($recipients)
]);
