<?php

defined('MOODLE_INTERNAL') || die();

$cols = array(
    'id' => get_string('table_head_id', 'local_bugreport'),
    'username' => get_string('table_head_username', 'local_bugreport'),
    'ts' => get_string('table_head_ts', 'local_bugreport'),
    'current_url' => get_string('table_head_report_image', 'local_bugreport'),
    'error_desc' => get_string('table_head_error_desc', 'local_bugreport'),
    'report_image' => get_string('table_head_report_image', 'local_bugreport'),
);

/**
 * Funkce pro vypsani informaci z logu -> prevod z JSONu na prosty text
 *
 * @param string $input
 * @param string $sep
 * @return string
 */
function getLog($input, $sep) {
    $log_output = array();
    $return = null;
    if (!empty($input)) {
        foreach ($input as $i) {
            $tmp = array();
            foreach ($i as $key => $value) {
                $tmp[] = '<b>' . $key . '</b>: ' . $value;
            }
            $log_output[] = implode('<br />', $tmp);
        }
        $return = implode($sep, $log_output);
    }

    return $return;
}

function getData($id = null) {
    /** @var moodle_database */
    global $CFG, $DB, $OUTPUT, $cols;
    include_once($CFG->dirroot . '/local/bugreport/moodledata.class.php');
    $moodledata = new Moodledata($DB);

    $sel_cols = implode(', ', array_keys($cols));

    $return = array();
    if ($id !== null) {
        $data = $DB->get_records_sql('SELECT * FROM {bugreport} WHERE id = ? LIMIT 1', array($id));
        return $data;
    } else {
        $data = $DB->get_records_sql('SELECT ' . $sel_cols . ' FROM {bugreport} ORDER BY id DESC');
    }

    foreach ($data as $d) {
        $rowcols = array();
        foreach ($d as $dt => $v) {
            $rowcols[$dt] = $v;
        }

        $moodle_file_id = (int) $rowcols['report_image'];
        $url = false;
        if ($moodle_file_id) {
            $moodledata->loadFile(array('id' => $moodle_file_id));
            if (!empty($moodledata)) {
                $url = $CFG->wwwroot . '/local/bugreport/image.php?id=' . $moodle_file_id;
            }
        }

        // odkaz
        if (isset($rowcols['current_url'])) {
            if ($url) {
                $rowcols['current_url'] = '<a href="' . $url . '" target="_blank">' . $moodledata->getFilename() . '</a>';
            } else {
                $rowcols['current_url'] = '&mdash;';
            }
        }

        // nahled
        if (isset($rowcols['report_image'])) {
            unset($rowcols['report_image']);
        }

        if (isset($rowcols['username'])) {
            if ($rowcols['username'] === '') {
                $rowcols['username'] = get_string('report_table_no_logged_user', 'local_bugreport');
            }
        }

        if (isset($rowcols['id'])) {
            $redirect_url = new moodle_url('/local/bugreport/bugreport_list.php?errid=' . $rowcols['id']);
            $delete_url = new moodle_url('/local/bugreport/bugreport_list.php?action=delete&errid=' . $rowcols['id']);
            $confirm = new confirm_action(get_string('bugreport_delete_confirm', 'local_bugreport'));

            $rowcols['detail'] = '<a href="' . $redirect_url . '">detail</a>';
            $rowcols['delete'] = $OUTPUT->action_icon($delete_url, new pix_icon('t/delete', get_string('delete')), $confirm);
        }

        $return[] = $rowcols;
    }

    return $return;
}

function deleteRecord($id) {
    global $DB;

    $return = null;

    if ($id) {
        $return = $DB->delete_records('bugreport', array('id' => (int) $id));
    } else {
        throw new Exception('ID must be integer type.');
    }

    return $return;
}

function displayTable() {
    global $cols, $OUTPUT;

    $table = new html_table();
    $table->id = "bugreport_preview";
    $table->attributes['class'] = 'generaltable';
    $table->tablealign = 'center';
    $table->summary = get_string('bugreport_title', 'local_bugreport');
    $table->head = array();
    $table->data = getData();

    foreach ($cols as $key => $col) {
        if ($key !== 'report_image') {
            $table->head[] = $col;
        }
    }
    $table->head[] = get_string('table_head_detail', 'local_bugreport');
    $table->head[] = get_string('table_head_action', 'local_bugreport');

    echo html_writer::tag('div', html_writer::table($table));
}

function displayDetail($errid) {
    global $CFG, $DB, $USER;
    include_once($CFG->dirroot . '/local/bugreport/moodledata.class.php');
    $moodledata = new Moodledata($DB);
    $redirect_url = new moodle_url('/local/bugreport/bugreport_list.php');

    $data_output = getData($errid);
    $table = '';

    foreach ($data_output as $data) {
        $log_array = json_decode($data->log, true);
        $profile_link = new moodle_url('/user/profile.php?id=' . $data->userid);

        ($data->username === '') ? $data->username = get_string('report_table_no_logged_user', 'local_bugreport') : $data->username = $data->username;
        $moodle_file_id = (int) $data->report_image;
        $url = false;
        $filename = false;
        if ($moodle_file_id) {
            $moodledata->loadFile(array('id' => $moodle_file_id));
            if (!empty($moodledata)) {
                $url = $CFG->wwwroot . '/local/bugreport/image.php?id=' . $moodle_file_id;
                $filename = $moodledata->getFilename();
            }
        }

        $moodle_file_id_orig = (int) $data->report_image_o;
        $url_orig = false;
        $filename_orig = false;
        if ($moodle_file_id_orig) {
            $moodledata->loadFile(array('id' => $moodle_file_id_orig));
            if (!empty($moodledata)) {
                $url_orig = $CFG->wwwroot . '/local/bugreport/image.php?id=' . $moodle_file_id_orig;
                $filename_orig = $moodledata->getFilename();
            }
        }

        $original_screen = '';

        if ($url_orig !== false) {
            $original_screen = '<a href="' . $url_orig . '" target="_blank">' . get_string('table_head_report_image_orig_a', 'local_bugreport') . '</a>';
        }

        $table .= '<table class="reportErrorTable no-shadow">';
        $table .= '<tbody>';
        $table .= '<tr><td colspan="3" class="content_center"><h2>' . get_string('bugreport_title_nr', 'local_bugreport', array('id' => $_GET['errid'])) . '</h2><a href="' . $redirect_url . '" class="button_back">' . get_string('table_head_detail_link', 'local_bugreport') . '</a></td></tr>';
        $table .= '<tr><td rowspan="10" class="content_center"><a href="' . $url . '" target="_blank"><img src="' . $url . '" alt="" title="' . get_string('table_head_report_image', 'local_bugreport') . '" width="350" /></a><br />' . $original_screen . '</td></tr>';
        $table .= '<tr><td class="td_left">' . get_string('table_head_current_url', 'local_bugreport') . '</td><td><a href="' . $data->current_url . '" target="_blank">' . $data->current_url . '</a></td></tr>';
        $table .= '<tr><td class="td_left">' . get_string('table_head_browser', 'local_bugreport') . '</td><td>' . $data->browser . '</td></tr>';
        $table .= '<tr><td class="td_left">' . get_string('table_head_system', 'local_bugreport') . '</td><td>' . $data->system . '</td></tr>';
        $table .= '<tr><td class="td_left">' . get_string('table_head_screen_res', 'local_bugreport') . '</td><td>' . $data->screen_res . '</td></tr>';
        if ($data->userid !== '0') {
            $send_message_link = new moodle_url('/message/index.php?id=' . $data->userid);
            $table .= '<tr>';
            $table .= '<td class="td_left">' . get_string('table_head_username', 'local_bugreport') . '</td>';
            $table .= '<td>';
            $table .= '<a href="' . $profile_link . '" target="_blank">' . $data->username . ' (id: ' . $data->userid . ')</a> ';
            if ($data->userid !== $USER->id) {
                $table .= '<a href="' . $send_message_link . '" class="bugreport_detail_send_message_a">' . get_string('table_detail_send_message', 'local_bugreport') . '</a>';
            }
            $table .= '</td>';
            $table .= '</tr>';
        } else {
            $table .= '<tr><td class="td_left">' . get_string('table_head_username', 'local_bugreport') . '</td><td>' . $data->username . '</td></tr>';
        }
        $table .= '<tr><td class="td_left">' . get_string('table_head_error_desc', 'local_bugreport') . '</td><td>' . $data->error_desc . '</td></tr>';
        $table .= '<tr><td class="td_left">' . get_string('table_head_report_image', 'local_bugreport') . '</td><td><a href="' . $url . '" target="_blank">' . $filename . '</a></td></tr>';
        
        $table .= '<tr><td class="td_left">' . get_string('table_head_report_image_orig', 'local_bugreport') . '</td>';
        if($url_orig !== false) {
            $table .= '<td><a href="' . $url_orig . '" target="_blank">' . $filename_orig . '</a></td>';
        } else {
            $table .= '<td>&mdash;</td>';
        }
        $table .= '</tr>';
        
        if ($log_array !== null) {
            $table .= '<tr><td class="td_left">' . get_string('table_head_log', 'local_bugreport') . '</td><td>' . getLog($log_array, '<br />-----------------------------<br />') . '</td></tr>';
        }
        $table .= '</tbody>';
        $table .= '</table>';
    }

    echo $table;
}