<?php
// This file is part of Moodle - http://moodle.org/

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

// Status messages
$string['messagesent'] = 'ההודעה נשלחה בהצלחה ל-{$a} סטודנט/ים';
$string['messagefailed'] = 'נכשל לשלוח הודעה ל-{$a} סטודנט/ים';
$string['sendingsuccess'] = 'נשלחו: {$a->sent}, נכשלו: {$a->failed} מתוך {$a->total} נמענים';
