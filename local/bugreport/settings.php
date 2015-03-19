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
 * Add page to admin menu.
 *
 * @package    local
 * @subpackage adminer
 * @copyright  2013 PragoData
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

// needs this condition or there is error on login page
if ($hassiteconfig) {
    
    /* Pole : Pozice tlacitka */
    $rbb_button_position = array(
        'left' => get_string('button_position_style_left', 'local_bugreport'),
        'right' => get_string('button_position_style_right', 'local_bugreport')
    );
    
    /* Pole : Barevna schemata */
    $rbb_button_schemas = array(
        'style_default' => get_string('button_style_default', 'local_bugreport'),
        'style_default_red' => get_string('button_style_red', 'local_bugreport'),
        'style_default_green' => get_string('button_style_green', 'local_bugreport'),
        'style_default_blue' => get_string('button_style_blue', 'local_bugreport')
    );
    
    /* Pole : Ikony tlacitka */
    $rbb_button_icons = array(
        'btn_white' => get_string('button_icon_style_white', 'local_bugreport'),
        'btn_red' => get_string('button_icon_style_red', 'local_bugreport'),
        'btn_green' => get_string('button_icon_style_green', 'local_bugreport'),
        'btn_blue' => get_string('button_icon_style_blue', 'local_bugreport')
    );
    
    /* Pole : Moznosti zobrazeni vypisu */
    $rbb_output_show = array(
        '0' => get_string('output_show_info_opt_no', 'local_bugreport'),
        '1' => get_string('output_show_info_opt_yes', 'local_bugreport')
    );
    
    $settings = new admin_settingpage('local_bugreport', get_string('pluginname_settings', 'local_bugreport'));
    $ADMIN->add('localplugins', $settings);

    /* Obecne nastaveni tlacitka */
    $settings->add(new admin_setting_heading('button_global_info_text', get_string('button_global_info_text_header', 'local_bugreport'), ''));
    
    $settings->add(new admin_setting_configcheckbox('show_unregistered', get_string('show_unregistered_name', 'local_bugreport'), get_string('show_unregistered_desc', 'local_bugreport'), 'false', 'true', 'false'));
    
    $settings->add(new admin_setting_configselect('button_position', get_string('button_position_name', 'local_bugreport'), get_string('button_position_desc', 'local_bugreport'), 'right', $rbb_button_position));
    $settings->add(new admin_setting_configselect('button_scheme', get_string('button_scheme_name', 'local_bugreport'), get_string('button_scheme_desc', 'local_bugreport'), 'style_default', $rbb_button_schemas));
    $settings->add(new admin_setting_configtext('button_title', get_string('button_title_name', 'local_bugreport'), get_string('button_title_desc', 'local_bugreport'), 'Našli jste chybu'));
    $settings->add(new admin_setting_configtext('button_desc', get_string('button_desc_name', 'local_bugreport'), get_string('button_desc_desc', 'local_bugreport'), ''));
    
    /* Vlastni styl tlacitka */
    $settings->add(new admin_setting_heading('button_info_text', get_string('button_info_text_header', 'local_bugreport'), ''));
    
    $settings->add(new admin_setting_configselect('button_icon', get_string('button_icon_name', 'local_bugreport'), get_string('button_icon_desc', 'local_bugreport'), 'btn_white', $rbb_button_icons));
    $settings->add(new admin_setting_configcolourpicker('button_bgcolor', get_string('button_bgcolor_name', 'local_bugreport'), get_string('button_bgcolor_desc', 'local_bugreport'), '#ffffff', null));
    $settings->add(new admin_setting_configcolourpicker('button_fontcolor', get_string('button_fontcolor_name', 'local_bugreport'), get_string('button_fontcolor_desc', 'local_bugreport'), '#ff6b00', null));
    
    /* Nastaveni vypisu */
    $settings->add(new admin_setting_heading('output_global_info_text', get_string('output_global_info_text_header', 'local_bugreport'), ''));
    $settings->add(new admin_setting_configselect('output_show_info', get_string('output_show_info_name', 'local_bugreport'), get_string('output_show_info_desc', 'local_bugreport'), '1', $rbb_output_show));
    $settings->add(new admin_setting_configtext('output_show_delay', get_string('output_show_delay_name', 'local_bugreport'), get_string('output_show_delay_desc', 'local_bugreport'), '10'));
    $settings->add(new admin_setting_configtext('output_log_count', get_string('output_log_count_name', 'local_bugreport'), get_string('output_log_count_desc', 'local_bugreport'), '0'));
    
    /* Nastaveni odesilani mailu */
    $settings->add(new admin_setting_heading('send_mail_info_text', get_string('send_mail_info_text_header', 'local_bugreport'), ''));
    $settings->add(new admin_setting_configcheckbox('send_mail_sendall', get_string('send_mail_sendall_name', 'local_bugreport'), get_string('send_mail_sendall_desc', 'local_bugreport'), 'false', 'true', 'false'));
    $settings->add(new admin_setting_configtext('send_mail_from_adress', get_string('send_mail_from_adress_name', 'local_bugreport'), get_string('send_mail_from_adress_desc', 'local_bugreport'),''));
    $settings->add(new admin_setting_configtext('send_mail_recipients', get_string('send_mail_recipients_name', 'local_bugreport'), get_string('send_mail_recipients_desc', 'local_bugreport'),''));
    $settings->add(new admin_setting_configcheckbox('send_mail_show_detaillink', get_string('send_mail_show_detaillink_name', 'local_bugreport'), get_string('send_mail_show_detaillink_desc', 'local_bugreport'), 'false', 'true', 'false'));
    
    /* Nastaveni FAQ */
    $settings->add(new admin_setting_heading('faq_global_info_text', get_string('faq_global_info_text_header', 'local_bugreport'), ''));    
    $settings->add(new admin_setting_confightmleditor('faq_summary', get_string('faq_summary_name', 'local_bugreport'), get_string('faq_summary_desc', 'local_bugreport'), ''));
}
