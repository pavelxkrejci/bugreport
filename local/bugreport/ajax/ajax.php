<?php

require_once(dirname(__FILE__) . '/../../../config.php');
require_once('../functionlib.php');
$task = $_POST['task'];

switch ($task) {
    case 'getFaq':
        echo json_encode(getFaq());
        exit();
        break;
    case 'configButton':
        echo json_encode(configButton());
        exit();
        break;
    case 'getFormData':
        getFormData();
        break;
}

/**
 * Funkce, ktera vraci seznam FAQ otazek z konfigurace
 *
 * @global type $CFG
 * @return string
 */
function getFaq() {
    global $CFG;
    $faq = $CFG->faq_summary;

    return $faq;
}

/**
 * Konfigurace tlacitka
 *
 * @global type $CFG
 * @return string
 */
function configButton() {
    $config = array();

    global $CFG;

    $pos = $CFG->button_position;               // $pos = 'right';
    $colorScheme = $CFG->button_scheme;         // $colorScheme = 'style_default'; , $colorScheme = 'style_default_red'; , $colorScheme = 'style_default_green'; , $colorScheme = 'style_default_blue';
    $text = $CFG->button_title;                 // $text = 'Reportovat chybu';
    $title = $CFG->button_desc;
    $icon = $CFG->button_icon;                  // $icon = 'btn_white';
    $color = $CFG->button_bgcolor;              // $color = '#000';
    $fontColor = $CFG->button_fontcolor;        // $fontColor = '#ff6b00';

    $config['pos'] = $pos;
    $config['icon'] = $icon;
    $config['text'] = $text;
    $config['title'] = $title;
    $config['color'] = $color;
    $config['fontColor'] = $fontColor;
    $config['colorScheme'] = $colorScheme;

    return $config;
}

/**
 * Odeslani dat z formulare, jejich zpracovani a ulozeni
 *
 * @global type $DB
 * @global type $CFG
 * @global type $USER
 */
function getFormData() {
    /** @var moodle_database */
    global $DB;
    global $CFG;
    global $USER;
    global $SITE;

    include_once($CFG->dirroot . '/local/bugreport/moodledata.class.php');
    $moodledata = new Moodledata($DB);

    $errorObject = new stdClass();
    $baseSite = $_POST['root'] . '/local/bugreport/';

    $timestamp = date('Ymd-his');
    $errorDesc = $_POST['popis'];
    isset($USER->username) ? $username = $USER->username : $username = '';
    $userid = $USER->id;
    $currentUrl = $_POST['aktualniUrl'];
    $randomNumber = rand(100, 10000);

    $filePath = 'exported';
    $fileName = 'e_report_img_' . $username . '_' . $timestamp . '_' . $randomNumber . '.png';
    $fileName_orig = 'e_report_img_' . $username . '_' . $timestamp . '_' . $randomNumber . '_orig.png';

    /* Naplneni objektu */
    $errorObject->username = $username;
    $errorObject->userid = $userid;
    $errorObject->current_url = $currentUrl;
    $errorObject->report_image = $baseSite . $filePath . '/' . $fileName;
    $errorObject->report_image = $baseSite . $filePath . '/' . $fileName_orig;
    $errorObject->error_desc = $errorDesc;
    $errorObject->browser = $_POST['browser'];
    $errorObject->system = getOS();
    $errorObject->log = '';
    $errorObject->screen_res = $_POST['screen_res'];
    $errorObject->browser_res = $_POST['browser_res'];
    $errorObject->ts = date('Y-m-d H:i:s');

    /* Pokud je nastaven pocet zaznamu vypisovanych z logu vetsi nez 0 -> zjisti se logy z databaze */
    if ($CFG->output_log_count > 0 && $USER->id !== 0) {
        $log_output = $DB->get_records_sql('SELECT id, module, action, url, info FROM {log} WHERE userid = ? ORDER BY id DESC LIMIT ' . $CFG->output_log_count, array($USER->id));
        $errorObject->log = json_encode($log_output);
    }

    /* Base-64 string z POSTu */
    $filteredData = substr($_POST['screenshot'], strpos($_POST['screenshot'], ",") + 1);
    $filteredData_orig = substr($_POST['screenshot_original'], strpos($_POST['screenshot_original'], ",") + 1);

    /* Rozkodovani stringu */
    $unencodedData = base64_decode($filteredData);
    $unencodedData_orig = base64_decode($filteredData_orig);

    /* Ulozeni obrazku */
    $context_id = 1;
    $component = 'local_bugreport';
    $filearea = 'bugreport';
    $itemid = 0;
    $itemid_orig = 1;
    $filepath = '/exported/';

    $file_string = $unencodedData;
    $filename = $fileName;

    $file_string_orig = $unencodedData_orig;
    $filename_orig = $fileName_orig;

    $stored_file = $moodledata->insertFile($context_id, $component, $filearea, $itemid, $filepath, $filename, $file_string);
    $errorObject->report_image = $stored_file->getId();

    $stored_file_orig = $moodledata->insertFile($context_id, $component, $filearea, $itemid_orig, $filepath, $filename_orig, $file_string_orig);
    $errorObject->report_image_o = $stored_file_orig->getId();

    /* Pokud neni prazdny objekt s chybou, ulozime jej do databaze */
    if (isset($errorObject)) {
        $lastinsertid = $DB->insert_record('bugreport', $errorObject);  // $DB->insert_record('bugreport', $errorObject, false);
        
        // a v pripade, ze jsou vyplneni prijemci odesleme mail
        if($CFG->send_mail_recipients != '') {
            $mailer = get_mailer();
            
            // Vytvoreni pole prijemcu
            $recipient_array = explode(';', $CFG->send_mail_recipients);
            
            // Nastaveni odesilatele
            if($CFG->send_mail_from_adress != '') {
                $send_from = $CFG->send_mail_from_adress;
            } else {
                $send_from = $CFG->noreplyaddress;
            }
            
            // Zobrazit v prijemcich vschny maily
            $send_all = $CFG->send_mail_sendall;
            
            // Detail chyboveho hlaseni
            $detail_link = $_POST['root'] . '/local/bugreport/bugreport_list.php?errid=' . $lastinsertid;
            
            // Predmet mailu
            $mail_subject = get_string('send_mail_subject', 'local_bugreport', array('id' => $lastinsertid, 'web' => $SITE->fullname));
            
            // Telo mailu
            $mail_message = '';            
            $mail_message .= get_string('table_head_h1', 'local_bugreport', $lastinsertid);
            
            // Pokud je povoleno zobrazeni odkazu na detail chyboveho hlaseni, pridame jej do tela zpravy
            if($CFG->send_mail_show_detaillink == 'true') {
                $mail_message .= get_string('table_head_h1_link', 'local_bugreport', $detail_link);
            }
            $mail_message .= '<table>';
            $mail_message .= '<tr><td>' . get_string('table_head_id', 'local_bugreport') . '</td><td>' . $lastinsertid . '</td></tr>';
            $mail_message .= '<tr><td>' . get_string('table_head_current_url', 'local_bugreport') . '</td><td><a href="' . $errorObject->current_url . '">' . $errorObject->current_url . '</a></td></tr>';
            $mail_message .= '<tr><td>' . get_string('table_head_username', 'local_bugreport') . '</td><td>' . $errorObject->username . '</td></tr>';
            $mail_message .= '<tr><td>' . get_string('table_head_error_desc', 'local_bugreport') . '</td><td>' . $errorObject->error_desc . '</td></tr>';
            $mail_message .= '<tr><td>' . get_string('table_head_browser', 'local_bugreport') . '</td><td>' . $errorObject->browser . '</td></tr>';
            $mail_message .= '<tr><td>' . get_string('table_head_system', 'local_bugreport') . '</td><td>' . $errorObject->system . '</td></tr>';
            $mail_message .= '</table>';
            
            // Seznam vsech prijemcu
            $copy_to = array();            
            foreach ($recipient_array as $recipient) {
                $copy_to[] = $recipient;
            }

            $mailer->SetFrom($send_from);
            $mailer->IsHTML(true);
            $mailer->Subject = $mail_subject;
            $mailer->Body = $mail_message;

            // Pro kazdeho prijemce...
            foreach ($recipient_array as $recipient) {
                // ...zjistime, zda se maji zobrazit vsichni prijemci v kolonce "prijemce". Pokud ano, pridame je do seznamu
                if ($send_all == 'true') {
                    foreach ($copy_to as $cc) {
                        if ($cc != $recipient) {
                            $mailer->AddAddress($cc);
                        }
                    }
                } else {
                    
                    // pokud ne, smazeme seznam prijmcu a v kolonce "prijemce" bude jenom jeden
                    $mailer->ClearAddresses();
                    $mailer->AddAddress($recipient);
                    $mailer->Send();
                }
            }

            // finalni odeslani mailu v pripade, ze se nemaji zobrazovat vsichni prijemci
            if ($send_all == 'true') {
                $mailer->Send();
            }
        }
    }

    /* Provede se presmerovani na aktualni URL */
    if (false/*$CFG->output_show_info === '1'*/) {
        redirect($currentUrl, renderErrorReport($lastinsertid), (int) $CFG->output_show_delay);
    } else {
        redirect($currentUrl, get_string('output_alert_notice', 'local_bugreport'));
    }

    exit();
}

/**
 * Zjisteni operacniho systemu
 *
 * @return string
 */
function getOS() {

    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    $os_platform = "Unknown OS Platform";
    $os_array = array(
        '/windows nt 6.2/i' => 'Windows 8',
        '/windows nt 6.1/i' => 'Windows 7',
        '/windows nt 6.0/i' => 'Windows Vista',
        '/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
        '/windows nt 5.1/i' => 'Windows XP',
        '/windows xp/i' => 'Windows XP',
        '/windows nt 5.0/i' => 'Windows 2000',
        '/windows me/i' => 'Windows ME',
        '/win98/i' => 'Windows 98',
        '/win95/i' => 'Windows 95',
        '/win16/i' => 'Windows 3.11',
        '/macintosh|mac os x/i' => 'Mac OS X',
        '/mac_powerpc/i' => 'Mac OS 9',
        '/linux/i' => 'Linux',
        '/ubuntu/i' => 'Ubuntu',
        '/iphone/i' => 'iPhone',
        '/ipod/i' => 'iPod',
        '/ipad/i' => 'iPad',
        '/android/i' => 'Android',
        '/blackberry/i' => 'BlackBerry',
        '/webos/i' => 'Mobile'
    );

    foreach ($os_array as $regex => $value) {
        if (preg_match($regex, $user_agent)) {
            $os_platform = $value;
        }
    }

    return $os_platform;
}

function renderErrorReport($id) {
    /** @var moodle_database */
    global $DB;
    global $CFG;
    global $USER;
    global $PAGE;

    //$PAGE->requires->css('/local/bugreport/css/report_table.css');

    $return = '';

    /* Podle ID z GETu si vypiseme patricny radek databaze */
    $data_output = $DB->get_records_sql('SELECT * FROM {bugreport} WHERE id = ? LIMIT 1', array($id));

    foreach ($data_output as $data) {
        $log_array = json_decode($data->log, true);
        $profile_link = new moodle_url('/user/profile.php?id=' . $data->userid);

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

        $return .= '<h1>' . get_string('report_table_heading', 'local_bugreport') . '</h1>';
        $return .= '<span class="redirect_info">' . get_string('report_table_redirect_info', 'local_bugreport', $CFG->output_show_delay) . '</span>';
        $return .= '<table class="reportErrorTable no-shadow">';
        $return .= '<tbody>';
        $return .= '<tr><td colspan="2" class="content_center"><a href="' . $url . '" target="_blank"><img src="' . $url . '" alt="" title="Náhled obrazovky s chybou" width="350" /></a></td></tr>';
        $return .= '<tr><td class="td_left">' . get_string('report_table_url', 'local_bugreport') . '</td><td><a href="' . $data->current_url . '" target="_blank">' . $data->current_url . '</a></td></tr>';
        $return .= '<tr><td class="td_left">' . get_string('report_table_browser', 'local_bugreport') . '</td><td>' . $data->browser . '</td></tr>';
        $return .= '<tr><td class="td_left">' . get_string('report_table_system', 'local_bugreport') . '</td><td>' . $data->system . '</td></tr>';
        $return .= '<tr><td class="td_left">' . get_string('report_table_screen_res', 'local_bugreport') . '</td><td>' . $data->screen_res . '</td></tr>';
        if ($data->userid !== '0') {
            $return .= '<tr><td class="td_left">' . get_string('report_table_username', 'local_bugreport') . '</td><td><a href="' . $profile_link . '" target="_blank">' . $data->username . ' (id: ' . $data->userid . ')</a></td></tr>';
        } else {
            $return .= '<tr><td class="td_left">' . get_string('report_table_username', 'local_bugreport') . '</td><td>' . $data->username . '</td></tr>';
        }
        $return .= '<tr><td class="td_left">' . get_string('report_table_error_detail', 'local_bugreport') . '</td><td>' . $data->error_desc . '</td></tr>';
        $return .= '<tr><td class="td_left">' . get_string('report_table_preview', 'local_bugreport') . '</td><td><a href="' . $url . '" target="_blank">' . $filename . '</a></td></tr>';
        if ($CFG->output_log_count > 0 && $USER->id !== 0) {
            $return .= '<tr><td class="td_left">' . get_string('report_table_log', 'local_bugreport') . '</td><td>' . getLog($log_array, '<br />-----------------------------<br />') . '</td></tr>';
        }
        $return .= '</tbody>';
        $return .= '</table>';
    }

    return $return;
}
