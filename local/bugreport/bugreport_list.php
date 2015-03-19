<?php
global $PAGE, $OUTPUT, $CFG;

require_once('../../config.php');
require_login();

$context = context_system::instance();
$PAGE->set_context($context);

$base_url = new moodle_url('/local/bugreport/bugreport_list.php');
$PAGE->set_url($base_url);
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('bugreport_title', 'local_bugreport'));
$PAGE->set_heading(get_string('bugreport_title', 'local_bugreport'));
// $PAGE->requires->css('/local/bugreport/css/report_table.css');

include($CFG->dirroot.'/local/bugreport/functionlib.php');

if(!empty($_GET['errid'])) {
    if(isset($_GET['action']) && $_GET['action'] == 'delete') {
        $redirect_url = new moodle_url('/local/bugreport/bugreport_list.php');
        if(deleteRecord($_GET['errid'])) {
            redirect($redirect_url, get_string('bugreport_delete_message', 'local_bugreport'));
        }
    }
}

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('bugreport_title', 'local_bugreport'));

if(!empty($_GET['errid'])) {
    $PAGE->set_title(get_string('bugreport_title_nr', 'local_bugreport', array('id' => $_GET['errid'])));
    $PAGE->set_heading(get_string('bugreport_title_nr', 'local_bugreport', array('id' => $_GET['errid'])));    
    displayDetail($_GET['errid']);
} else {
    displayTable();
}

echo $OUTPUT->footer();
exit();
