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

$string['templatename'] = 'Template Name';
$string['templatename_desc'] = 'WhatsApp template name (optional). If set, messages will be sent using this template instead of plain text.';
$string['templatelang'] = 'Template Language';
$string['templatelang_desc'] = 'Template language code (e.g., en, en_US, he_IL). Default: en';

$string['templatecontent'] = 'Template Content (for reference)';
$string['templatecontent_desc'] = 'Paste your WhatsApp template here for reference. The plugin will parse {{placeholders}} and map them to available data.<br><br>
<strong>Available placeholders:</strong><br>
{firstname} - Student first name<br>
{lastname} - Student last name<br>
{fullname} - Student full name<br>
{email} - Student email<br>
{coursename} - Course name<br>
{courseid} - Course ID<br>
{courseshortname} - Course short name<br>
{message} - The actual message content entered by the teacher<br>
{teachername} - Teacher full name<br>
{sitename} - Site name<br><br>
<strong>Example template:</strong><br>
שלום {firstname}, להלן הודעה שנשלחה מצוות התמיכה שלנו:<br>
*תוכן:* {message}<br>
בברכה, צוות התמיכה.';

$string['debugmode'] = 'Debug Mode';
$string['debugmode_desc'] = 'Enable debug logging to PHP error log. Use this to troubleshoot issues with message sending.';

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
