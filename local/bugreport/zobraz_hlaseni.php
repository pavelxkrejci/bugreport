<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" href="css/report_table.css" type="text/css" />
        <title>Podrobnosti o hlášené chybě</title>
    </head>
    <body>
        <?php
        require_once(dirname(__FILE__) . '/../../config.php');
        require_once('functionlib.php');

        /** @var moodle_database */
        global $DB;
        global $CFG;
        global $USER;

        $id = $_GET['id'];

        /* Podle ID z GETu si vypiseme patricny radek databaze */
        $data_output = $DB->get_records_sql('SELECT * FROM {bugreport} WHERE id = ? LIMIT 1', array($id));

        foreach ($data_output as $data) {
            $log_array = json_decode($data->log, true);
            $profile_link = new moodle_url('/user/profile.php?id=' . $data->userid);

            global $CFG, $DB;
            include_once($CFG->dirroot . '/local/bugreport/moodledata.class.php');
            $moodledata = new Moodledata($DB);
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

            ($data->username === '') ? $data->username = get_string('report_table_no_logged_user', 'local_bugreport') : $data->username = $data->username;

            echo '<h1>' . get_string('report_table_heading', 'local_bugreport') . '</h1>';
            echo '<table class="reportErrorTable">';
            echo '<tbody>';
            echo '<tr><td colspan="2" class="content_center"><a href="' . $url . '" target="_blank"><img src="' . $url . '" alt="" title="Náhled obrazovky s chybou" width="350" /></a></td></tr>';
            echo '<tr><td class="td_left">' . get_string('report_table_url', 'local_bugreport') . '</td><td><a href="' . $data->current_url . '" target="_blank">' . $data->current_url . '</a></td></tr>';
            echo '<tr><td class="td_left">' . get_string('report_table_browser', 'local_bugreport') . '</td><td>' . $data->browser . '</td></tr>';
            echo '<tr><td class="td_left">' . get_string('report_table_system', 'local_bugreport') . '</td><td>' . $data->system . '</td></tr>';
            echo '<tr><td class="td_left">' . get_string('report_table_screen_res', 'local_bugreport') . '</td><td>' . $data->screen_res . '</td></tr>';
            echo '<tr><td class="td_left">' . get_string('report_table_browser_res', 'local_bugreport') . '</td><td>' . $data->browser_res . '</td></tr>';
            if ($data->userid !== '0') {
                echo '<tr><td class="td_left">' . get_string('report_table_username', 'local_bugreport') . '</td><td><a href="' . $profile_link . '" target="_blank">' . $data->username . ' (id: ' . $data->userid . ')</a></td></tr>';
            } else {
                echo '<tr><td class="td_left">' . get_string('report_table_username', 'local_bugreport') . '</td><td>' . $data->username . '</td></tr>';
            }
            echo '<tr><td class="td_left">' . get_string('report_table_error_detail', 'local_bugreport') . '</td><td>' . $data->error_desc . '</td></tr>';
            echo '<tr><td class="td_left">' . get_string('report_table_preview', 'local_bugreport') . '</td><td><a href="' . $url . '" target="_blank">' . $filename . '</a></td></tr>';
            if ($CFG->output_log_count > 0 && $USER->id !== 0) {
                echo '<tr><td class="td_left">' . get_string('report_table_log', 'local_bugreport') . '</td><td>' . getLog($log_array, '<br />-----------------------------<br />') . '</td></tr>';
            }
            echo '</tbody>';
            echo '</table>';
        }
        ?>
    </body>
</html>
