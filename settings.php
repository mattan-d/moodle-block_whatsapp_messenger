<?php
// This file is part of Moodle - http://moodle.org/

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    
    // Access Token
    $settings->add(new admin_setting_configtext(
        'block_whatsapp_messenger/accesstoken',
        get_string('accesstoken', 'block_whatsapp_messenger'),
        get_string('accesstoken_desc', 'block_whatsapp_messenger'),
        '',
        PARAM_TEXT
    ));
    
    // Phone Number ID
    $settings->add(new admin_setting_configtext(
        'block_whatsapp_messenger/phonenumberid',
        get_string('phonenumberid', 'block_whatsapp_messenger'),
        get_string('phonenumberid_desc', 'block_whatsapp_messenger'),
        '',
        PARAM_TEXT
    ));
    
    // API Version
    $settings->add(new admin_setting_configtext(
        'block_whatsapp_messenger/apiversion',
        get_string('apiversion', 'block_whatsapp_messenger'),
        get_string('apiversion_desc', 'block_whatsapp_messenger'),
        'v17.0',
        PARAM_TEXT
    ));
    
    $settings->add(new admin_setting_configtext(
        'block_whatsapp_messenger/templatename',
        get_string('templatename', 'block_whatsapp_messenger'),
        get_string('templatename_desc', 'block_whatsapp_messenger'),
        '',
        PARAM_TEXT
    ));
    
    $settings->add(new admin_setting_configtext(
        'block_whatsapp_messenger/templatelang',
        get_string('templatelang', 'block_whatsapp_messenger'),
        get_string('templatelang_desc', 'block_whatsapp_messenger'),
        'en',
        PARAM_TEXT
    ));
    
    $settings->add(new admin_setting_configcheckbox(
        'block_whatsapp_messenger/debugmode',
        get_string('debugmode', 'block_whatsapp_messenger'),
        get_string('debugmode_desc', 'block_whatsapp_messenger'),
        0
    ));
}
