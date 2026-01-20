<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * External API for sending WhatsApp messages.
 *
 * @package    block_whatsapp_messenger
 * @copyright  2024 CentricApp LTD (https://centricapp.co.il)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_whatsapp_messenger\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;
use core_external\external_single_structure;
use context_course;
use curl;

/**
 * External service for sending WhatsApp messages.
 */
class send_message extends external_api {

    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course ID'),
            'recipient' => new external_value(PARAM_INT, 'Recipient user ID (0 for all)'),
            'message' => new external_value(PARAM_TEXT, 'Message content'),
        ]);
    }

    /**
     * Send WhatsApp message to recipients.
     *
     * @param int $courseid Course ID
     * @param int $recipient Recipient user ID (0 for all)
     * @param string $message Message content
     * @return array Result of the operation
     */
    public static function execute($courseid, $recipient, $message) {
        global $DB, $USER;

        // Validate parameters.
        $params = self::validate_parameters(self::execute_parameters(), [
            'courseid' => $courseid,
            'recipient' => $recipient,
            'message' => $message,
        ]);

        // Get context and check permissions.
        $context = context_course::instance($params['courseid']);
        self::validate_context($context);
        require_capability('block/whatsapp_messenger:sendmessage', $context);

        $debugmode = get_config('block_whatsapp_messenger', 'debugmode');
        
        if ($debugmode) {
            error_log('[WhatsApp Messenger] Execute called - Course: ' . $params['courseid'] . 
                     ', Recipient: ' . $params['recipient'] . ', User: ' . $USER->id);
        }

        // Get configuration.
        $accesstoken = get_config('block_whatsapp_messenger', 'accesstoken');
        $phonenumberid = get_config('block_whatsapp_messenger', 'phonenumberid');
        $templatename = get_config('block_whatsapp_messenger', 'templatename');
        $templatelang = get_config('block_whatsapp_messenger', 'templatelang');
        $templatecontent = get_config('block_whatsapp_messenger', 'templatecontent');

        if (empty($accesstoken) || empty($phonenumberid)) {
            return [
                'success' => false,
                'message' => get_string('apicredentialsnotconfigured', 'block_whatsapp_messenger'),
                'successcount' => 0,
                'failcount' => 0,
            ];
        }

        // Get recipients.
        if ($params['recipient'] > 0) {
            $user = $DB->get_record('user', ['id' => $params['recipient']], '*', MUST_EXIST);
            $recipients = [$user];
        } else {
            // Get all enrolled users with phone numbers.
            $enrolledusers = get_enrolled_users($context, '', 0, 'u.*', null, 0, 0, true);
            $recipients = array_filter($enrolledusers, function($user) {
                return !empty($user->phone1);
            });
        }

        if (empty($recipients)) {
            return [
                'success' => false,
                'message' => get_string('noreciplentswithphonenumbersfound', 'block_whatsapp_messenger'),
                'successcount' => 0,
                'failcount' => 0,
            ];
        }

        // Get course info.
        $course = $DB->get_record('course', ['id' => $params['courseid']], '*', MUST_EXIST);

        // Send messages.
        $successcount = 0;
        $failcount = 0;
        $lasterror = '';

        foreach ($recipients as $user) {
            $phone = self::format_phone_number($user->phone1);
            
            if (empty($phone)) {
                $failcount++;
                continue;
            }

            $result = self::send_whatsapp_message(
                $accesstoken,
                $phonenumberid,
                $phone,
                $params['message'],
                $user,
                $course,
                $templatename,
                $templatelang,
                $templatecontent,
                $debugmode
            );

            if ($result['success']) {
                $successcount++;
                // Log to database.
                self::log_message($params['courseid'], $user->id, $params['message'], 'sent');
            } else {
                $failcount++;
                $lasterror = $result['error'];
                self::log_message($params['courseid'], $user->id, $params['message'], 'failed');
            }
        }

        $totalmessages = $successcount + $failcount;
        $message = "Sent: {$successcount}/{$totalmessages}";
        if ($failcount > 0) {
            $message .= " - Failed: {$failcount}";
            if (!empty($lasterror)) {
                $message .= " - Error: {$lasterror}";
            }
        }

        return [
            'success' => $failcount === 0,
            'message' => $message,
            'successcount' => $successcount,
            'failcount' => $failcount,
        ];
    }

    /**
     * Format phone number for WhatsApp API.
     *
     * @param string $phone Phone number
     * @return string Formatted phone number
     */
    private static function format_phone_number($phone) {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($phone) > 8) {
            return $phone;
        }
        return '';
    }

    /**
     * Send WhatsApp message via API.
     *
     * @param string $accesstoken API access token
     * @param string $phonenumberid Phone number ID
     * @param string $to Recipient phone number
     * @param string $message Message content
     * @param object $user User object
     * @param object $course Course object
     * @param string $templatename Template name
     * @param string $templatelang Template language
     * @param string $templatecontent Template content
     * @param bool $debugmode Debug mode enabled
     * @return array Result with success and error
     */
    private static function send_whatsapp_message($accesstoken, $phonenumberid, $to, $message, 
                                                   $user, $course, $templatename, $templatelang, 
                                                   $templatecontent, $debugmode) {
        $url = "https://graph.facebook.com/v21.0/{$phonenumberid}/messages";

        // Prepare replacement values.
        $replacements = [
            '{firstname}' => $user->firstname,
            '{lastname}' => $user->lastname,
            '{fullname}' => fullname($user),
            '{username}' => $user->username,
            '{email}' => $user->email,
            '{message}' => $message,
            '{coursename}' => $course->fullname,
            '{courseid}' => $course->id,
            '{courseshortname}' => $course->shortname,
            '{date}' => userdate(time(), get_string('strftimedate', 'langconfig')),
            '{datetime}' => userdate(time(), get_string('strftimedatetime', 'langconfig')),
            '{time}' => userdate(time(), get_string('strftimetime', 'langconfig')),
        ];

        // If template is configured, use it.
        if (!empty($templatename) && !empty($templatelang)) {
            if (!empty($templatecontent)) {
                // Parse template content to extract parameters.
                $parameters = [];
                $templatetext = $templatecontent;
                
                foreach ($replacements as $placeholder => $value) {
                    if (strpos($templatetext, $placeholder) !== false) {
                        $parameters[] = $value;
                    }
                }

                $data = [
                    'messaging_product' => 'whatsapp',
                    'to' => $to,
                    'type' => 'template',
                    'template' => [
                        'name' => $templatename,
                        'language' => [
                            'code' => $templatelang,
                        ],
                        'components' => [
                            [
                                'type' => 'body',
                                'parameters' => array_map(function($value) {
                                    return ['type' => 'text', 'text' => $value];
                                }, $parameters),
                            ],
                        ],
                    ],
                ];
            } else {
                // No template content, send with message only.
                $data = [
                    'messaging_product' => 'whatsapp',
                    'to' => $to,
                    'type' => 'template',
                    'template' => [
                        'name' => $templatename,
                        'language' => [
                            'code' => $templatelang,
                        ],
                        'components' => [
                            [
                                'type' => 'body',
                                'parameters' => [
                                    ['type' => 'text', 'text' => $user->firstname],
                                    ['type' => 'text', 'text' => $message],
                                ],
                            ],
                        ],
                    ],
                ];
            }
        } else {
            // No template, send plain text.
            $data = [
                'messaging_product' => 'whatsapp',
                'to' => $to,
                'type' => 'text',
                'text' => ['body' => $message],
            ];
        }

        if ($debugmode) {
            error_log('[WhatsApp Messenger] Sending to ' . $to . ': ' . json_encode($data));
        }

        $curl = new curl();
        $curl->setHeader([
            'Authorization: Bearer ' . $accesstoken,
            'Content-Type: application/json',
        ]);

        $response = $curl->post($url, json_encode($data));
        $httpcode = $curl->get_info()['http_code'];

        if ($debugmode) {
            error_log('[WhatsApp Messenger] Response code: ' . $httpcode . ', Response: ' . $response);
        }

        if ($httpcode == 200) {
            return ['success' => true, 'error' => ''];
        } else {
            $responsedata = json_decode($response, true);
            $errormessage = 'Unknown error';
            
            if (isset($responsedata['error']['message'])) {
                $errormessage = $responsedata['error']['message'];
            } else if (isset($responsedata['error']['error_user_msg'])) {
                $errormessage = $responsedata['error']['error_user_msg'];
            }

            return ['success' => false, 'error' => $errormessage];
        }
    }

    /**
     * Log message to database.
     *
     * @param int $courseid Course ID
     * @param int $userid User ID
     * @param string $message Message content
     * @param string $status Status (sent/failed)
     */
    private static function log_message($courseid, $userid, $message, $status) {
        global $DB, $USER;

        $record = new \stdClass();
        $record->courseid = $courseid;
        $record->userid = $userid;
        $record->senderid = $USER->id;
        $record->message = $message;
        $record->status = $status;
        $record->timecreated = time();

        $DB->insert_record('block_whatsapp_messenger_log', $record);
    }

    /**
     * Returns description of method result value.
     *
     * @return external_single_structure
     */
    public static function execute_returns() {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Success status'),
            'message' => new external_value(PARAM_TEXT, 'Result message'),
            'successcount' => new external_value(PARAM_INT, 'Number of successful sends'),
            'failcount' => new external_value(PARAM_INT, 'Number of failed sends'),
        ]);
    }
}
