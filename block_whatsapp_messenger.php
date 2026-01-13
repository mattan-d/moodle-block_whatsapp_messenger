<?php
// This file is part of Moodle - http://moodle.org/

defined('MOODLE_INTERNAL') || die();

class block_whatsapp_messenger extends block_base {
    
    public function init() {
        $this->title = get_string('pluginname', 'block_whatsapp_messenger');
    }
    
    public function get_content() {
        global $COURSE, $PAGE, $DB;
        
        if ($this->content !== null) {
            return $this->content;
        }
        
        $this->content = new stdClass();
        $this->content->text = '';
        $this->content->footer = '';
        
        // Check if user has capability to send messages
        $context = context_course::instance($COURSE->id);
        if (!has_capability('block/whatsapp_messenger:sendmessage', $context)) {
            $this->content->text = get_string('nopermission', 'block_whatsapp_messenger');
            return $this->content;
        }
        
        // Check if plugin is configured
        $accesstoken = get_config('block_whatsapp_messenger', 'accesstoken');
        $phonenumberid = get_config('block_whatsapp_messenger', 'phonenumberid');
        
        if (empty($accesstoken) || empty($phonenumberid)) {
            $this->content->text = get_string('notconfigured', 'block_whatsapp_messenger');
            return $this->content;
        }
        
        // Get enrolled students with phone numbers
        $sql = "SELECT u.id, u.firstname, u.lastname, u.phone1, u.phone2
                FROM {user} u
                JOIN {user_enrolments} ue ON u.id = ue.userid
                JOIN {enrol} e ON ue.enrolid = e.id
                WHERE e.courseid = :courseid
                AND u.deleted = 0
                AND u.suspended = 0
                AND (u.phone1 IS NOT NULL AND u.phone1 != '')
                ORDER BY u.lastname, u.firstname";
        
        $students = $DB->get_records_sql($sql, ['courseid' => $COURSE->id]);
        
        // Load JavaScript module
        $PAGE->requires->js_call_amd('block_whatsapp_messenger/messenger', 'init', [$COURSE->id]);
        
        // Build the form
        $html = html_writer::start_div('whatsapp-messenger-block');
        
        $html .= html_writer::start_tag('form', [
            'id' => 'whatsapp-message-form',
            'class' => 'whatsapp-form',
            'onsubmit' => 'return false;' // Prevent default submission
        ]);
        
        // Recipient selection
        $html .= html_writer::start_div('form-group');
        $html .= html_writer::label(get_string('recipient', 'block_whatsapp_messenger'), 'recipient-select');
        $html .= html_writer::start_tag('select', [
            'id' => 'recipient-select',
            'name' => 'recipient',
            'class' => 'form-control'
        ]);
        
        $html .= html_writer::tag('option', get_string('allstudents', 'block_whatsapp_messenger'), ['value' => 'all']);
        
        foreach ($students as $student) {
            $phone = !empty($student->phone1) ? $student->phone1 : $student->phone2;
            $displayname = fullname($student) . ' (' . $phone . ')';
            $html .= html_writer::tag('option', $displayname, ['value' => $student->id]);
        }
        
        $html .= html_writer::end_tag('select');
        $html .= html_writer::end_div();
        
        // Message textarea
        $html .= html_writer::start_div('form-group');
        $html .= html_writer::label(get_string('message', 'block_whatsapp_messenger'), 'message-text');
        $html .= html_writer::tag('textarea', '', [
            'id' => 'message-text',
            'name' => 'message',
            'class' => 'form-control',
            'rows' => 4,
            'placeholder' => get_string('messageplaceholder', 'block_whatsapp_messenger')
        ]);
        $html .= html_writer::end_div();
        
        // Send button
        $html .= html_writer::start_div('form-group');
        $html .= html_writer::tag('button', get_string('sendmessage', 'block_whatsapp_messenger'), [
            'type' => 'submit',
            'class' => 'btn btn-primary',
            'id' => 'send-whatsapp-btn'
        ]);
        $html .= html_writer::end_div();
        
        // Status message area
        $html .= html_writer::div('', 'whatsapp-status', ['id' => 'whatsapp-status']);
        
        $html .= html_writer::end_tag('form');
        $html .= html_writer::end_div();
        
        $this->content->text = $html;
        
        return $this->content;
    }
    
    public function applicable_formats() {
        return ['course' => true, 'course-category' => false, 'site' => false];
    }
    
    public function has_config() {
        return true;
    }
}
