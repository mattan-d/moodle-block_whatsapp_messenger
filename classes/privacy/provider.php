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
 * Privacy Subsystem implementation for block_whatsapp_messenger.
 *
 * @package    block_whatsapp_messenger
 * @copyright  2024 CentricApp LTD (https://centricapp.co.il)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_whatsapp_messenger\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;

/**
 * Privacy provider for block_whatsapp_messenger.
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\request\plugin\provider {

    /**
     * Returns meta data about this system.
     *
     * @param collection $collection The initialised collection to add items to.
     * @return collection A listing of user data stored through this system.
     */
    public static function get_metadata(collection $collection): collection {
        // WhatsApp messages log table.
        $collection->add_database_table(
            'block_whatsapp_messenger_log',
            [
                'userid' => 'privacy:metadata:block_whatsapp_log:userid',
                'courseid' => 'privacy:metadata:block_whatsapp_log:courseid',
                'senderid' => 'privacy:metadata:block_whatsapp_log:senderid',
                'phone' => 'privacy:metadata:block_whatsapp_log:phone',
                'message' => 'privacy:metadata:block_whatsapp_log:message',
                'status' => 'privacy:metadata:block_whatsapp_log:status',
                'error' => 'privacy:metadata:block_whatsapp_log:error',
                'timecreated' => 'privacy:metadata:block_whatsapp_log:timecreated',
            ],
            'privacy:metadata:block_whatsapp_log'
        );

        // External service - WhatsApp Business API.
        $collection->add_external_location_link(
            'whatsapp_business_api',
            [
                'phone' => 'privacy:metadata:whatsapp_business_api:phone',
                'message' => 'privacy:metadata:whatsapp_business_api:message',
                'firstname' => 'privacy:metadata:whatsapp_business_api:firstname',
                'lastname' => 'privacy:metadata:whatsapp_business_api:lastname',
                'coursename' => 'privacy:metadata:whatsapp_business_api:coursename',
            ],
            'privacy:metadata:whatsapp_business_api'
        );

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid The user to search.
     * @return contextlist The contextlist containing the list of contexts used in this plugin.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();

        // Messages sent by the user.
        $sql = "SELECT DISTINCT ctx.id
                  FROM {context} ctx
                  JOIN {course} c ON c.id = ctx.instanceid AND ctx.contextlevel = :contextcourse
                  JOIN {block_whatsapp_messenger_log} wl ON wl.courseid = c.id
                 WHERE wl.senderid = :userid";

        $contextlist->add_from_sql($sql, [
            'contextcourse' => CONTEXT_COURSE,
            'userid' => $userid,
        ]);

        // Messages received by the user.
        $sql = "SELECT DISTINCT ctx.id
                  FROM {context} ctx
                  JOIN {course} c ON c.id = ctx.instanceid AND ctx.contextlevel = :contextcourse
                  JOIN {block_whatsapp_messenger_log} wl ON wl.courseid = c.id
                 WHERE wl.userid = :userid";

        $contextlist->add_from_sql($sql, [
            'contextcourse' => CONTEXT_COURSE,
            'userid' => $userid,
        ]);

        return $contextlist;
    }

    /**
     * Get the list of users who have data within a context.
     *
     * @param userlist $userlist The userlist containing the list of users who have data in this context/plugin combination.
     */
    public static function get_users_in_context(userlist $userlist) {
        $context = $userlist->get_context();

        if (!$context instanceof \context_course) {
            return;
        }

        // Users who sent messages.
        $sql = "SELECT wl.senderid as userid
                  FROM {block_whatsapp_messenger_log} wl
                 WHERE wl.courseid = :courseid";

        $userlist->add_from_sql('userid', $sql, ['courseid' => $context->instanceid]);

        // Users who received messages.
        $sql = "SELECT wl.userid
                  FROM {block_whatsapp_messenger_log} wl
                 WHERE wl.courseid = :courseid";

        $userlist->add_from_sql('userid', $sql, ['courseid' => $context->instanceid]);
    }

    /**
     * Export all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts to export information for.
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $user = $contextlist->get_user();
        $userid = $user->id;

        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel != CONTEXT_COURSE) {
                continue;
            }

            $courseid = $context->instanceid;

            // Export messages sent by the user.
            $sql = "SELECT *
                      FROM {block_whatsapp_messenger_log}
                     WHERE courseid = :courseid AND senderid = :userid
                  ORDER BY timecreated ASC";

            $messages = $DB->get_records_sql($sql, [
                'courseid' => $courseid,
                'userid' => $userid,
            ]);

            if (!empty($messages)) {
                $data = [];
                foreach ($messages as $message) {
                    $recipient = $DB->get_record('user', ['id' => $message->userid], 'firstname, lastname');
                    $data[] = [
                        'recipient' => $recipient ? fullname($recipient) : 'Unknown',
                        'phone' => $message->phone,
                        'message' => $message->message,
                        'status' => $message->status,
                        'error' => $message->error,
                        'timecreated' => \core_privacy\local\request\transform::datetime($message->timecreated),
                    ];
                }
                writer::with_context($context)->export_data(
                    [get_string('privacy:metadata:block_whatsapp_log', 'block_whatsapp_messenger'), 'sent'],
                    (object)['messages' => $data]
                );
            }

            // Export messages received by the user.
            $sql = "SELECT *
                      FROM {block_whatsapp_messenger_log}
                     WHERE courseid = :courseid AND userid = :userid
                  ORDER BY timecreated ASC";

            $messages = $DB->get_records_sql($sql, [
                'courseid' => $courseid,
                'userid' => $userid,
            ]);

            if (!empty($messages)) {
                $data = [];
                foreach ($messages as $message) {
                    $sender = $DB->get_record('user', ['id' => $message->senderid], 'firstname, lastname');
                    $data[] = [
                        'sender' => $sender ? fullname($sender) : 'Unknown',
                        'message' => $message->message,
                        'status' => $message->status,
                        'timecreated' => \core_privacy\local\request\transform::datetime($message->timecreated),
                    ];
                }
                writer::with_context($context)->export_data(
                    [get_string('privacy:metadata:block_whatsapp_log', 'block_whatsapp_messenger'), 'received'],
                    (object)['messages' => $data]
                );
            }
        }
    }

    /**
     * Delete all data for all users in the specified context.
     *
     * @param \context $context The specific context to delete data for.
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        global $DB;

        if ($context->contextlevel != CONTEXT_COURSE) {
            return;
        }

        $DB->delete_records('block_whatsapp_messenger_log', ['courseid' => $context->instanceid]);
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts and user information to delete information for.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $userid = $contextlist->get_user()->id;

        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel != CONTEXT_COURSE) {
                continue;
            }

            $courseid = $context->instanceid;

            // Delete messages sent by the user.
            $DB->delete_records('block_whatsapp_messenger_log', [
                'courseid' => $courseid,
                'senderid' => $userid,
            ]);

            // Delete messages received by the user.
            $DB->delete_records('block_whatsapp_messenger_log', [
                'courseid' => $courseid,
                'userid' => $userid,
            ]);
        }
    }

    /**
     * Delete multiple users within a single context.
     *
     * @param approved_userlist $userlist The approved context and user information to delete information for.
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        $context = $userlist->get_context();

        if ($context->contextlevel != CONTEXT_COURSE) {
            return;
        }

        $courseid = $context->instanceid;
        $userids = $userlist->get_userids();

        if (empty($userids)) {
            return;
        }

        list($insql, $inparams) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);

        // Delete messages sent by users.
        $DB->delete_records_select(
            'block_whatsapp_messenger_log',
            "courseid = :courseid AND senderid $insql",
            array_merge(['courseid' => $courseid], $inparams)
        );

        // Delete messages received by users.
        $DB->delete_records_select(
            'block_whatsapp_messenger_log',
            "courseid = :courseid AND userid $insql",
            array_merge(['courseid' => $courseid], $inparams)
        );
    }
}
