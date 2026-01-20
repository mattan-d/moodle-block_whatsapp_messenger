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
 * English language strings for the WhatsApp Messenger block.
 *
 * @package    block_whatsapp_messenger
 * @copyright  2024 CentricApp LTD (https://centricapp.co.il)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

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
{sitename} - Site name<br>
{date} - Current date (short format)<br>
{datetime} - Current date and time<br>
{time} - Current time<br><br>
<strong>Example template:</strong><br>
שלום {firstname}, להלן הודעה שנשלחה מצוות התמיכה שלנו:<br>
*תוכן:* {message}<br>
*תאריך:* {date}<br>
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
$string['apicredentialsnotconfigured'] = 'WhatsApp API credentials not configured';
$string['noreciplentswithphonenumbersfound'] = 'No recipients with phone numbers found';

// Status messages
$string['messagesent'] = 'Message sent successfully to {$a} student(s)';
$string['messagefailed'] = 'Failed to send message to {$a} student(s)';
$string['sendingsuccess'] = 'Sent: {$a->sent}, Failed: {$a->failed} out of {$a->total} recipients';

// Privacy API
$string['privacy:metadata:block_whatsapp_log'] = 'WhatsApp messages log';
$string['privacy:metadata:block_whatsapp_log:userid'] = 'The ID of the user who sent the message';
$string['privacy:metadata:block_whatsapp_log:courseid'] = 'The ID of the course where the message was sent';
$string['privacy:metadata:block_whatsapp_log:recipient'] = 'The ID of the user who received the message';
$string['privacy:metadata:block_whatsapp_log:message'] = 'The content of the message sent';
$string['privacy:metadata:block_whatsapp_log:status'] = 'The delivery status of the message (success/failed)';
$string['privacy:metadata:block_whatsapp_log:response'] = 'The API response from WhatsApp';
$string['privacy:metadata:block_whatsapp_log:timecreated'] = 'The time when the message was sent';

$string['privacy:metadata:whatsapp_business_api'] = 'The WhatsApp Business API is used to send messages to students. Personal data is transmitted to WhatsApp servers.';
$string['privacy:metadata:whatsapp_business_api:phone'] = 'The recipient\'s phone number';
$string['privacy:metadata:whatsapp_business_api:message'] = 'The message content being sent';
$string['privacy:metadata:whatsapp_business_api:firstname'] = 'The recipient\'s first name (if used in template)';
$string['privacy:metadata:whatsapp_business_api:lastname'] = 'The recipient\'s last name (if used in template)';
$string['privacy:metadata:whatsapp_business_api:coursename'] = 'The course name (if used in template)';
