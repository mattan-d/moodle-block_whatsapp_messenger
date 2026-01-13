<?php
// This file is part of Moodle - http://moodle.org/

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/filelib.php');

$debugmode = get_config('block_whatsapp_messenger', 'debugmode');

function debug_log($message, $data = null) {
    global $debugmode;
    if ($debugmode) {
        $logdata = [
            'time' => date('Y-m-d H:i:s'),
            'message' => $message
        ];
        if ($data !== null) {
            $logdata['data'] = $data;
        }
        error_log('[WhatsApp Messenger Debug] ' . json_encode($logdata));
    }
}

debug_log('Script started', $_POST);

$courseid = required_param('courseid', PARAM_INT);
$recipient = required_param('recipient', PARAM_TEXT);
$message = required_param('message', PARAM_TEXT);

debug_log('Parameters received', ['courseid' => $courseid, 'recipient' => $recipient]);

require_login();
require_sesskey();

debug_log('Authentication passed');

$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
$context = context_course::instance($courseid);

require_capability('block/whatsapp_messenger:sendmessage', $context);

debug_log('Capability check passed');

$accesstoken = get_config('block_whatsapp_messenger', 'accesstoken');
$phonenumberid = get_config('block_whatsapp_messenger', 'phonenumberid');
$apiversion = get_config('block_whatsapp_messenger', 'apiversion') ?: 'v17.0';
$templatename = get_config('block_whatsapp_messenger', 'templatename');
$templatelang = get_config('block_whatsapp_messenger', 'templatelang') ?: 'en';
$templatecontent = get_config('block_whatsapp_messenger', 'templatecontent');

debug_log('Configuration loaded', [
    'apiversion' => $apiversion,
    'templatename' => $templatename,
    'templatelang' => $templatelang,
    'has_template_content' => !empty($templatecontent),
    'has_token' => !empty($accesstoken),
    'has_phoneid' => !empty($phonenumberid)
]);

if (empty($accesstoken) || empty($phonenumberid)) {
    debug_log('Configuration missing');
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
    debug_log('Selected all students', ['count' => count($recipients)]);
} else {
    $user = $DB->get_record('user', ['id' => $recipient], '*', MUST_EXIST);
    if (!empty($user->phone1) || !empty($user->phone2)) {
        $recipients = [$user];
        debug_log('Selected single user', ['userid' => $user->id, 'name' => fullname($user)]);
    }
}

if (empty($recipients)) {
    debug_log('No recipients found');
    echo json_encode(['success' => false, 'error' => get_string('norecipients', 'block_whatsapp_messenger')]);
    exit;
}

// Send messages
$success_count = 0;
$failed_count = 0;
$api_url = "https://graph.facebook.com/{$apiversion}/{$phonenumberid}/messages";

debug_log('Starting message sending', ['api_url' => $api_url, 'recipient_count' => count($recipients)]);

$template_params = [];
if (!empty($templatecontent)) {
    preg_match_all('/{(\w+)}/', $templatecontent, $matches);
    $template_params = $matches[1];
    debug_log('Template placeholders found', ['placeholders' => $template_params]);
}

foreach ($recipients as $user) {
    $phone = !empty($user->phone1) ? $user->phone1 : $user->phone2;
    
    $phone = preg_replace('/[^0-9+]/', '', $phone);
    
    debug_log('Processing recipient', ['userid' => $user->id, 'phone' => $phone]);
    
    if (!empty($templatename) && !empty($templatecontent)) {
        $parameters = [];
        foreach ($template_params as $placeholder) {
            $value = '';
            switch ($placeholder) {
                case 'firstname':
                    $value = $user->firstname;
                    break;
                case 'lastname':
                    $value = $user->lastname;
                    break;
                case 'fullname':
                    $value = fullname($user);
                    break;
                case 'email':
                    $value = $user->email;
                    break;
                case 'coursename':
                    $value = $course->fullname;
                    break;
                case 'courseid':
                    $value = $course->id;
                    break;
                case 'courseshortname':
                    $value = $course->shortname;
                    break;
                case 'message':
                    $value = $message;
                    break;
                case 'teachername':
                    $value = fullname($USER);
                    break;
                case 'sitename':
                    global $SITE;
                    $value = $SITE->fullname;
                    break;
                default:
                    $value = '';
            }
            
            $parameters[] = [
                'type' => 'text',
                'text' => $value
            ];
        }
        
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
                        'parameters' => $parameters
                    ]
                ]
            ]
        ];
        debug_log('Using template message with dynamic params', ['template' => $templatename, 'param_count' => count($parameters)]);
    } else {
        $data = [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'text',
            'text' => [
                'body' => $message
            ]
        ];
        debug_log('Using text message');
    }
    
    $curl = new curl();
    $curl->setHeader([
        'Authorization: Bearer ' . $accesstoken,
        'Content-Type: application/json'
    ]);
    
    try {
        $response = $curl->post($api_url, json_encode($data));
        debug_log('API response received', ['response' => $response]);
        
        $result = json_decode($response, true);
        
        if (isset($result['messages'])) {
            $success_count++;
            debug_log('Message sent successfully', ['messageid' => $result['messages'][0]['id'] ?? '']);
            
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
            $error_message = 'Unknown error';
            if (isset($result['error'])) {
                $error_message = $result['error']['message'] ?? $result['error']['error_user_msg'] ?? json_encode($result['error']);
            } elseif (is_string($result)) {
                $error_message = $result;
            }
            debug_log('Message failed', ['error' => $result]);
            
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
        $error_message = $e->getMessage();
        debug_log('Exception occurred', ['error' => $error_message]);
        
        $log = new stdClass();
        $log->courseid = $courseid;
        $log->userid = $user->id;
        $log->phone = $phone;
        $log->message = $message;
        $log->status = 'failed';
        $log->error = $error_message;
        $log->timecreated = time();
        $log->senderid = $USER->id;
        
        $DB->insert_record('block_whatsapp_messenger_log', $log);
    }
}

$response = [
    'success' => $failed_count === 0,
    'sent' => $success_count,
    'failed' => $failed_count,
    'total' => count($recipients)
];

if ($failed_count > 0 && isset($error_message)) {
    $response['error'] = $error_message;
}

debug_log('Script completed', $response);

echo json_encode($response);
