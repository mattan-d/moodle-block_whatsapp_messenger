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
 * Hebrew language strings for the WhatsApp Messenger block.
 *
 * @package    block_whatsapp_messenger
 * @copyright  2024 CentricApp LTD (https://centricapp.co.il)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'שליחת הודעות WhatsApp';
$string['whatsapp_messenger:addinstance'] = 'הוספת בלוק שליחת הודעות WhatsApp';
$string['whatsapp_messenger:myaddinstance'] = 'הוספת בלוק שליחת הודעות WhatsApp ללוח המחוונים';
$string['whatsapp_messenger:sendmessage'] = 'שליחת הודעות WhatsApp לסטודנטים';

// Settings
$string['accesstoken'] = 'טוקן גישה של WhatsApp';
$string['accesstoken_desc'] = 'טוקן הגישה שלך ל-WhatsApp Business API';
$string['phonenumberid'] = 'מזהה מספר טלפון';
$string['phonenumberid_desc'] = 'מזהה מספר הטלפון שלך ב-WhatsApp Business';
$string['apiversion'] = 'גרסת API';
$string['apiversion_desc'] = 'גרסת WhatsApp API (ברירת מחדל: v17.0)';

$string['templatename'] = 'שם התבנית';
$string['templatename_desc'] = 'שם תבנית WhatsApp (אופציונלי). אם מוגדר, ההודעות יישלחו באמצעות תבנית זו במקום טקסט רגיל.';
$string['templatelang'] = 'שפת התבנית';
$string['templatelang_desc'] = 'קוד שפת התבנית (לדוגמה: en, en_US, he_IL). ברירת מחדל: en';

$string['templatecontent'] = 'תוכן התבנית (לעיון)';
$string['templatecontent_desc'] = 'הדביקו את תבנית WhatsApp שלכם כאן לעיון. התוסף יזהה את {{ממלאי המקום}} ויתאים אותם לנתונים הזמינים.<br><br>
<strong>ממלאי מקום זמינים:</strong><br>
{firstname} - שם פרטי של הסטודנט<br>
{lastname} - שם משפחה של הסטודנט<br>
{fullname} - שם מלא של הסטודנט<br>
{email} - דואר אלקטרוני של הסטודנט<br>
{coursename} - שם הקורס<br>
{courseid} - מזהה הקורס<br>
{courseshortname} - שם מקוצר של הקורס<br>
{message} - תוכן ההודעה שהוקלד על ידי המורה<br>
{teachername} - שם מלא של המורה<br>
{sitename} - שם האתר<br>
{date} - תאריך נוכחי (פורמט קצר)<br>
{datetime} - תאריך ושעה נוכחיים<br>
{time} - שעה נוכחית<br><br>
<strong>דוגמה לתבנית:</strong><br>
שלום {firstname}, להלן הודעה שנשלחה מצוות התמיכה שלנו:<br>
*תוכן:* {message}<br>
*תאריך:* {date}<br>
בברכה, צוות התמיכה.';

$string['debugmode'] = 'מצב ניפוי שגיאות';
$string['debugmode_desc'] = 'הפעלת רישום ניפוי שגיאות ללוג ה-PHP. השתמשו בזה כדי לפתור בעיות בשליחת הודעות.';

// Block content
$string['recipient'] = 'נמען';
$string['allstudents'] = 'כל הסטודנטים';
$string['message'] = 'הודעה';
$string['messageplaceholder'] = 'הקלידו את ההודעה שלכם כאן...';
$string['sendmessage'] = 'שלחו הודעה';
$string['nopermission'] = 'אין לכם הרשאה לשלוח הודעות WhatsApp.';
$string['notconfigured'] = 'שליחת הודעות WhatsApp לא מוגדרת. אנא פנו למנהל המערכת.';
$string['norecipients'] = 'לא נמצאו נמענים עם מספרי טלפון.';
$string['apicredentialsnotconfigured'] = 'אישורי WhatsApp API לא מוגדרים';
$string['noreciplentswithphonenumbersfound'] = 'לא נמצאו נמענים עם מספרי טלפון';

// Status messages
$string['messagesent'] = 'ההודעה נשלחה בהצלחה ל-{$a} סטודנט/ים';
$string['messagefailed'] = 'נכשל לשלוח הודעה ל-{$a} סטודנט/ים';
$string['sendingsuccess'] = 'נשלחו: {$a->sent}, נכשלו: {$a->failed} מתוך {$a->total} נמענים';

// Privacy API
$string['privacy:metadata:block_whatsapp_log'] = 'יומן הודעות WhatsApp';
$string['privacy:metadata:block_whatsapp_log:userid'] = 'מזהה המשתמש ששלח את ההודעה';
$string['privacy:metadata:block_whatsapp_log:courseid'] = 'מזהה הקורס בו נשלחה ההודעה';
$string['privacy:metadata:block_whatsapp_log:recipient'] = 'מזהה המשתמש שקיבל את ההודעה';
$string['privacy:metadata:block_whatsapp_log:message'] = 'תוכן ההודעה שנשלחה';
$string['privacy:metadata:block_whatsapp_log:status'] = 'סטטוס משלוח ההודעה (הצלחה/כישלון)';
$string['privacy:metadata:block_whatsapp_log:response'] = 'תגובת ה-API מ-WhatsApp';
$string['privacy:metadata:block_whatsapp_log:timecreated'] = 'הזמן בו נשלחה ההודעה';

$string['privacy:metadata:whatsapp_business_api'] = 'WhatsApp Business API משמש לשליחת הודעות לסטודנטים. מידע אישי מועבר לשרתי WhatsApp.';
$string['privacy:metadata:whatsapp_business_api:phone'] = 'מספר הטלפון של הנמען';
$string['privacy:metadata:whatsapp_business_api:message'] = 'תוכן ההודעה הנשלחת';
$string['privacy:metadata:whatsapp_business_api:firstname'] = 'השם הפרטי של הנמען (אם משתמשים בתבנית)';
$string['privacy:metadata:whatsapp_business_api:lastname'] = 'שם המשפחה של הנמען (אם משתמשים בתבנית)';
$string['privacy:metadata:whatsapp_business_api:coursename'] = 'שם הקורס (אם משתמשים בתבנית)';
