<?php
// This file is part of Moodle - http://moodle.org/

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    
    // WhatsApp Access Token
    $settings->add(new admin_setting_configtext(
        'block_whatsapp_messenger/whatsapp_access_token',
        get_string('accesstoken', 'block_whatsapp_messenger'),
        get_string('accesstoken_desc', 'block_whatsapp_messenger'),
        '',
        PARAM_TEXT
    ));

    // WhatsApp Phone Number ID
    $settings->add(new admin_setting_configtext(
        'block_whatsapp_messenger/whatsapp_phone_number_id',
        get_string('phonenumberid', 'block_whatsapp_messenger'),
        get_string('phonenumberid_desc', 'block_whatsapp_messenger'),
        '',
        PARAM_TEXT
    ));

    // Template Name (optional)
    $settings->add(new admin_setting_configtext(
        'block_whatsapp_messenger/template_name',
        get_string('templatename', 'block_whatsapp_messenger'),
        get_string('templatename_desc', 'block_whatsapp_messenger'),
        'hello_world',
        PARAM_TEXT
    ));

    // Template Language (optional)
    $settings->add(new admin_setting_configtext(
        'block_whatsapp_messenger/template_language',
        get_string('templatelanguage', 'block_whatsapp_messenger'),
        get_string('templatelanguage_desc', 'block_whatsapp_messenger'),
        'en_US',
        PARAM_TEXT
    ));

    // Use Template (yes/no)
    $settings->add(new admin_setting_configcheckbox(
        'block_whatsapp_messenger/use_template',
        get_string('usetemplate', 'block_whatsapp_messenger'),
        get_string('usetemplate_desc', 'block_whatsapp_messenger'),
        1
    ));
}
