<?php
// This file is part of Moodle - http://moodle.org/

defined('MOODLE_INTERNAL') || die();

class block_whatsapp_messenger extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_whatsapp_messenger');
    }

    public function get_content() {
        global $COURSE, $USER, $OUTPUT, $PAGE;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';

        // Only show in course context, not on site home
        if ($COURSE->id == SITEID) {
            $this->content->text = get_string('notavailable', 'block_whatsapp_messenger');
            return $this->content;
        }

        // Check if user has capability to send messages
        $context = context_course::instance($COURSE->id);
        if (!has_capability('block/whatsapp_messenger:sendmessage', $context)) {
            $this->content->text = get_string('nopermission', 'block_whatsapp_messenger');
            return $this->content;
        }

        // Check if WhatsApp credentials are configured
        $accesstoken = get_config('block_whatsapp_messenger', 'whatsapp_access_token');
        $phonenumberid = get_config('block_whatsapp_messenger', 'whatsapp_phone_number_id');
        
        if (empty($accesstoken) || empty($phonenumberid)) {
            $this->content->text = $OUTPUT->notification(
                get_string('notconfigured', 'block_whatsapp_messenger'),
                'warning'
            );
            return $this->content;
        }

        // Get enrolled students
        $enrolledusers = $this->get_enrolled_students($COURSE->id);
        
        if (empty($enrolledusers)) {
            $this->content->text = get_string('nostudents', 'block_whatsapp_messenger');
            return $this->content;
        }

        // Add required JavaScript
        $PAGE->requires->js_call_amd('block_whatsapp_messenger/messenger', 'init', [$COURSE->id]);

        // Build the content
        $this->content->text .= html_writer::start_div('whatsapp-messenger-block');
        
        // Message form
        $this->content->text .= html_writer::start_tag('form', [
            'id' => 'whatsapp-message-form',
            'class' => 'whatsapp-form'
        ]);
        
        // Student selection
        $this->content->text .= html_writer::tag('label', 
            get_string('selectstudents', 'block_whatsapp_messenger'), 
            ['for' => 'student-select']
        );
        
        $this->content->text .= html_writer::start_tag('select', [
            'id' => 'student-select',
            'name' => 'students[]',
            'multiple' => 'multiple',
            'class' => 'form-control',
            'size' => '5'
        ]);
        
        $this->content->text .= html_writer::tag('option', 
            get_string('allstudents', 'block_whatsapp_messenger'),
            ['value' => 'all']
        );
        
        foreach ($enrolledusers as $user) {
            $this->content->text .= html_writer::tag('option', 
                fullname($user) . ' (' . $user->phone1 . ')',
                ['value' => $user->id]
            );
        }
        
        $this->content->text .= html_writer::end_tag('select');
        
        // Message textarea
        $this->content->text .= html_writer::tag('label', 
            get_string('message', 'block_whatsapp_messenger'),
            ['for' => 'message-text', 'class' => 'mt-2']
        );
        
        $this->content->text .= html_writer::tag('textarea', '', [
            'id' => 'message-text',
            'name' => 'message',
            'rows' => '4',
            'class' => 'form-control',
            'placeholder' => get_string('messageplaceholder', 'block_whatsapp_messenger'),
            'required' => 'required'
        ]);
        
        // Send button
        $this->content->text .= html_writer::tag('button', 
            get_string('sendmessage', 'block_whatsapp_messenger'),
            [
                'type' => 'submit',
                'class' => 'btn btn-primary mt-2',
                'id' => 'send-whatsapp-btn'
            ]
        );
        
        $this->content->text .= html_writer::end_tag('form');
        
        // Status message area
        $this->content->text .= html_writer::div('', 'whatsapp-status mt-2', ['id' => 'whatsapp-status']);
        
        $this->content->text .= html_writer::end_div();

        return $this->content;
    }

    /**
     * Get enrolled students in the course with phone numbers
     */
    private function get_enrolled_students($courseid) {
        global $DB;

        $context = context_course::instance($courseid);
        
        // Get enrolled users with student role
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
                GROUP BY u.id, u.firstname, u.lastname, u.phone1, u.phone2
                ORDER BY u.lastname, u.firstname";

        $params = [
            'courseid' => $courseid,
            'contextlevel' => CONTEXT_COURSE,
            'contextid' => $courseid
        ];

        return $DB->get_records_sql($sql, $params);
    }

    public function applicable_formats() {
        return [
            'course-view' => true,
            'site' => false,
            'mod' => false,
            'my' => false
        ];
    }

    public function has_config() {
        return true;
    }

    public function instance_allow_multiple() {
        return false;
    }
}
