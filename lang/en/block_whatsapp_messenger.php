<?php
// This file is part of Moodle - http://moodle.org/

$string['pluginname'] = 'WhatsApp Messenger';
$string['whatsapp_messenger:addinstance'] = 'Add a new WhatsApp Messenger block';
$string['whatsapp_messenger:myaddinstance'] = 'Add a new WhatsApp Messenger block to Dashboard';
$string['whatsapp_messenger:sendmessage'] = 'Send WhatsApp messages to students';

// Settings
$string['accesstoken'] = 'WhatsApp Access Token';
$string['accesstoken_desc'] = 'Your WhatsApp Business API access token';
$string['phonenumberid'] = 'Phone Number ID';
$string['phonenumberid_desc'] = 'Your WhatsApp Business phone number ID';
$string['apiversion'] = 'API Version';
$string['apiversion_desc'] = 'WhatsApp API version (default: v17.0)';

// Block content
$string['recipient'] = 'Recipient';
$string['allstudents'] = 'All Students';
$string['message'] = 'Message';
$string['messageplaceholder'] = 'Type your message here...';
$string['sendmessage'] = 'Send Message';
$string['nopermission'] = 'You do not have permission to send WhatsApp messages.';
$string['notconfigured'] = 'WhatsApp Messenger is not configured. Please contact your administrator.';
$string['norecipients'] = 'No recipients found with phone numbers.';

// Status messages
$string['messagesent'] = 'Message sent successfully to {$a} student(s)';
$string['messagefailed'] = 'Failed to send message to {$a} student(s)';
$string['sendingsuccess'] = 'Sent: {$a->sent}, Failed: {$a->failed} out of {$a->total} recipients';
