<?php

defined('MOODLE_INTERNAL') || die();
require_once("$CFG->dirroot" . "/lib/navigationlib.php");

global $CFG, $PAGE;

/* if (!$PAGE->requires->is_head_done()){
    $PAGE->requires->css('/local/bugreport/css/errormodule.css');
} */

/* Export plugin hook to extend the main navigation, see also local/readme.txt */

function local_bugreport_extends_settings_navigation(settings_navigation $nav, context $context=null) {
    global $PAGE, $CFG, $USER;

    $admins = get_admins();

    // menu polozka
    $reports = $nav->find('reports', $nav::TYPE_SETTING);
    if ($reports && !empty($USER->id) && isset($admins[$USER->id])) {
        $url = new moodle_url('/local/bugreport/bugreport_list.php');
        $childnode = $reports::create(get_string('bugreport_menuitem', 'local_bugreport'), $url);
        $reports->add_node($childnode);
    }

    if ($USER->id != 0 || (!empty($CFG->show_unregistered) && $CFG->show_unregistered == 'true')) {
        /* @var $PAGE moodle_page */
        $PAGE->requires->data_for_js('wwwroot', $CFG->wwwroot);

        /* Prirazeni JS promennych */
        $PAGE->requires->data_for_js('js_header_faq', get_string('js_header_faq', 'local_bugreport'));        
        $PAGE->requires->data_for_js('js_header_type', get_string('js_header_type', 'local_bugreport'));
        $PAGE->requires->data_for_js('js_header_simple', get_string('js_header_simple', 'local_bugreport'));
        $PAGE->requires->data_for_js('js_header_screenshot', get_string('js_header_screenshot', 'local_bugreport'));
        $PAGE->requires->data_for_js('js_header_dialog', get_string('js_header_dialog', 'local_bugreport'));
        $PAGE->requires->data_for_js('js_label_dialog_screenshot', get_string('js_label_dialog_screenshot', 'local_bugreport'));
        $PAGE->requires->data_for_js('js_label_dialog_description', get_string('js_label_dialog_description', 'local_bugreport'));
        $PAGE->requires->data_for_js('js_button_continue', get_string('js_button_continue', 'local_bugreport'));
        $PAGE->requires->data_for_js('js_button_showfaq', get_string('js_button_showfaq', 'local_bugreport'));
        $PAGE->requires->data_for_js('js_button_send', get_string('js_button_send', 'local_bugreport'));
        $PAGE->requires->data_for_js('js_button_close', get_string('js_button_close', 'local_bugreport'));
        $PAGE->requires->data_for_js('js_button_type1', get_string('js_button_type1', 'local_bugreport'));
        $PAGE->requires->data_for_js('js_button_type2', get_string('js_button_type2', 'local_bugreport'));
        $PAGE->requires->data_for_js('js_string_marker_name', get_string('js_string_marker_name', 'local_bugreport'));
        $PAGE->requires->data_for_js('js_string_marker_hint', get_string('js_string_marker_hint', 'local_bugreport'));
        $PAGE->requires->data_for_js('js_screenshot_introtext', get_string('js_string_introtext', 'local_bugreport'));
        $PAGE->requires->data_for_js('js_string_browser_error', get_string('js_string_browser_error', 'local_bugreport'));
        $PAGE->requires->data_for_js('js_string_field_required', get_string('js_string_field_required', 'local_bugreport'));

        $PAGE->requires->data_for_js('js_faq_choice', get_string('js_faq_choice', 'local_bugreport'));
        $PAGE->requires->data_for_js('js_type_desc', get_string('js_type_desc', 'local_bugreport'));

        $PAGE->requires->js('/local/bugreport/js/jquery-1.9.1.js');
        //$PAGE->requires->js('/local/bugreport/js/jquery-ui-1.10.3.custom.js');
        $PAGE->requires->js('/local/bugreport/js/jquery-ui-1.11.2.min.js');
        $PAGE->requires->js('/local/bugreport/js/jquery.plugin.html2canvas.js');
        $PAGE->requires->js('/local/bugreport/js/jquery.autogrow-textarea.js');
        $PAGE->requires->js('/local/bugreport/js/jquery.mb.browser.min.js');
        $PAGE->requires->js('/local/bugreport/js/html5.js');
        $PAGE->requires->js('/local/bugreport/js/css3-mediaqueries.js');
        $PAGE->requires->js('/local/bugreport/js/html2canvas.js');
        $PAGE->requires->js('/local/bugreport/js/bugreport.js');
    }
}
