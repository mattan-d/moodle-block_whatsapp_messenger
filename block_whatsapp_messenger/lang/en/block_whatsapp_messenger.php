<?php
// This file is part of Moodle - http://moodle.org/

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'WhatsApp Messenger';
$string['whatsapp_messenger:addinstance'] = 'Add a new WhatsApp Messenger block';
$string['whatsapp_messenger:myaddinstance'] = 'Add WhatsApp Messenger block to Dashboard';
$string['whatsapp_messenger:sendmessage'] = 'Send WhatsApp messages to students';

// Settings
$string['accesstoken'] = 'WhatsApp Access Token';
$string['accesstoken_desc'] = 'Your WhatsApp Business API access token from Facebook';
$string['phonenumberid'] = 'WhatsApp Phone Number ID';
$string['phonenumberid_desc'] = 'Your WhatsApp Business phone number ID';
$string['templatename'] = 'Template Name';
$string['templatename_desc'] = 'WhatsApp message template name (if using templates)';
$string['templatelanguage'] = 'Template Language';
$string['templatelanguage_desc'] = 'Template language code (e.g., en_US, he)';
$string['usetemplate'] = 'Use WhatsApp Template';
$string['usetemplate_desc'] = 'Enable to use WhatsApp message templates instead of free text';

// Block content
$string['notavailable'] = 'This block is only available in courses';
$string['nopermission'] = 'You do not have permission to send WhatsApp messages';
$string['notconfigured'] = 'WhatsApp credentials are not configured. Please contact your administrator.';
$string['nostudents'] = 'No students with phone numbers enrolled in this course';
$string['selectstudents'] = 'Select Students:';
$string['allstudents'] = 'All Students';
$string['message'] = 'Message:';
$string['messageplaceholder'] = 'Enter your message here...';
$string['sendmessage'] = 'Send WhatsApp Message';

// Messages
$string['messagesent'] = 'Message sent successfully to {$a} student(s)';
$string['messagefailed'] = 'Failed to send message: {$a}';
$string['invalidphone'] = 'Invalid phone number format for user: {$a}';
$string['selectstudentserror'] = 'Please select at least one student';
$string['messageempty'] = 'Message cannot be empty';
