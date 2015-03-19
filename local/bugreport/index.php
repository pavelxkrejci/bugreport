<?php
global $PAGE, $OUTPUT, $DB, $CFG;

require_once('../../config.php');

$context = context_system::instance();
$PAGE->set_context($context);

$base_url = new moodle_url('/local/bugreport/index.php');
$PAGE->set_url($base_url);
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('pluginname', 'local_bugreport'));
$PAGE->set_heading(get_string('pluginname', 'local_bugreport'));

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('pluginname', 'local_bugreport'));

echo $OUTPUT->footer();
exit();
