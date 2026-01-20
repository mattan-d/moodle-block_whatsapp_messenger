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
 * Settings for the WhatsApp Messenger block.
 *
 * @package    block_whatsapp_messenger
 * @copyright  2024 CentricApp LTD (https://centricapp.co.il)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

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
    
    // Template Name
    $settings->add(new admin_setting_configtext(
        'block_whatsapp_messenger/templatename',
        get_string('templatename', 'block_whatsapp_messenger'),
        get_string('templatename_desc', 'block_whatsapp_messenger'),
        '',
        PARAM_TEXT
    ));
    
    // Template Language
    $settings->add(new admin_setting_configtext(
        'block_whatsapp_messenger/templatelang',
        get_string('templatelang', 'block_whatsapp_messenger'),
        get_string('templatelang_desc', 'block_whatsapp_messenger'),
        'en',
        PARAM_TEXT
    ));
    
    // Template Content
    $settings->add(new admin_setting_configtextarea(
        'block_whatsapp_messenger/templatecontent',
        get_string('templatecontent', 'block_whatsapp_messenger'),
        get_string('templatecontent_desc', 'block_whatsapp_messenger'),
        '',
        PARAM_RAW
    ));
    
    // Debug Mode
    $settings->add(new admin_setting_configcheckbox(
        'block_whatsapp_messenger/debugmode',
        get_string('debugmode', 'block_whatsapp_messenger'),
        get_string('debugmode_desc', 'block_whatsapp_messenger'),
        0
    ));
}
