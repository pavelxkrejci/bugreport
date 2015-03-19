<?php
require_once('../../config.php');

global $CFG, $DB;
include_once($CFG->dirroot.'/local/bugreport/moodledata.class.php');
$moodledata=new Moodledata($DB);

$id=(int)$_GET['id'];

header('Content-Type: image/png');
if($id){
	$moodledata->loadFile(array('id'=>$id));
	echo $moodledata->getFile()->get_content();
}