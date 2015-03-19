<?php

class Moodledata {

    private $db;

    /** @var \file_storage */
    private $file_storage;

    /** @var \stored_file */
    private $file;
    private $files;
    private $temp_dir;
    private $temp_dir_name = 'pdcon_moodledata';

    /** @var \stored_file */
    private $temp_moodle_file;
    private $temp_file_name;
    private $params = array('id', 'contenthash', 'pathnamehash', 'contextid', 'component', 'filearea', 'itemid', 'filepath', 'filename', 'userid', 'mimetype', 'source', 'license');
    private $id;
    private $contenthash;
    private $pathnamehash;
    private $contextid;
    private $component;
    private $filearea;
    private $itemid;
    private $filepath;
    private $filename;
    private $userid;
    private $load_return_info = false;
    private $order_by;
    private $group_by;
    private $limit;

    /*     * *********************************************************************** */
    /* magic methods */
    /*     * *********************************************************************** */

    /**
     * @param moodle_database $db
     */
    public function __construct(moodle_database $db) {
        global $CFG;
        $this->db = $db;
        $this->file_storage = get_file_storage();
        $this->temp_dir = $CFG->tempdir . "/" . $this->temp_dir_name;
    }

    /**
     * enable to call file methods (get_id, get_contenthash, etc.)
     * but format of names of methods is (getId, getContenthash, etc.)
     * @param string $name
     * @param array $arguments
     * @return string
     * @throws Exception
     */
    public function __call($name, $arguments) {
        $arguments = $arguments;
        $return = null;
        // get metody
        if (strpos($name, 'get') === 0) {
            $get_param = str_replace('get', false, $name);
            $this->controlParamName(strtolower($get_param));
            foreach ($this->params as $param) {
                if ($get_param === ucfirst($param)) {
                    $method_name = 'get_' . $param;
                    $return = $this->isFileLoaded()->file->$method_name();
                    break;
                }
            }
        } else {
            throw new Exception('Calling of unknown method "' . $name . '"!');
        }
        return $return;
    }

    /*     * *********************************************************************** */
    /* public methods */
    /*     * *********************************************************************** */

    /**
     * @param array $params ('id'=>'','contenthash'=>'','pathnamehash'=>'','contextid'=>'','component'=>'','filearea'=>'','itemid'=>'','filepath'=>'','filename'=>'','userid'=>'')
     * @param string $order_by
     * @param string $group_by
     * @param string $limit
     * @return \Moodledata or boolean if turn it on (setLoadReturnInfo)
     */
    public function loadFile($params, $order_by = null, $group_by = null, $limit = null) {
        $return = null;
        $this
                ->reset()
                ->controlParamArray($params)
                ->setParams($params)
                ->setOrderBy($order_by)
                ->setGroupBy($group_by)
                ->setLimit($limit)
                ->setFiles();
        if ($this->files && count($this->files) > 1) {
            throw new Exception('Load more than one file! (' . serialize($params) . ')');
        } elseif ($this->files) {
            $this->file = $this->files[0];
            if ($this->load_return_info) {
                $return = true;
            }
        } elseif ($this->load_return_info) {
            $return = false;
        } else {
            throw new Exception('No file loaded! (' . serialize($params) . ')');
        }
        return ($return === null ? $this : $return);
    }

    /**
     * @return \stored_file
     */
    public function getFile() {
        return $this->isFileLoaded()->file;
    }

    /**
     * @return string
     */
    public function getFileContent() {
        return $this->file->get_content();
    }

    public function getFileInfo() {
        $info = array();
        foreach ($this->params as $param_name) {
            $method_name = 'get_' . $param_name;
            $info[$param_name] = $this->file->$method_name();
        }
        return $info;
    }

    /**
     * nahradi obsah textoveho souboru
     * @param string $content
     * @return \Moodledata
     * @throws Exception
     */
    public function updateFileContent($content) {
        $mimetype = $this->file->get_mimetype();
        if ($mimetype === 'text/plain' || $mimetype === 'text/html') {
            $this
                    ->isFileLoaded()
                    ->setTempFileName()
                    ->createMoodleTempFile($content)
                    ->replaceContentByMoodleTempFile()
                    ->deleteMoodleTempFile()
                    ->actualizeFile()
            ;
        } else {
            throw new Exception('Update file content is posible only at txt or html file!');
        }
        return $this;
    }

    /**
     * @param array $params
     * @param string $order_by
     * @param string $group_by
     * @param string $limit
     * @return array of stored_file
     */
    public function getFiles($params, $order_by = null, $group_by = null, $limit = null) {
        return $this
                        ->reset()
                        ->controlParamArray($params)
                        ->setParams($params)
                        ->setOrderBy($order_by)
                        ->setGroupBy($group_by)
                        ->setLimit($limit)
                        ->setFiles()
                ->files;
    }

    /**
     *
     * @param string $context_id
     * @param string $component
     * @param string $filearea
     * @param integer $itemid
     * @param string $filepath
     * @param string $filename
     * @param string $file_string
     * @return \Moodledata
     */
    public function insertFile($context_id, $component, $filearea, $itemid, $filepath, $filename, $file_string) {
        $fileinfo = array(
            'contextid' => $context_id, // ID of context
            'component' => $component, // usually = table name
            'filearea' => $filearea, // usually = table name
            'itemid' => $itemid, // usually = ID of row in table
            'filepath' => $filepath, // any path beginning and ending in /
            'filename' => $filename); // any filename
        $this->file = $this->file_storage->create_file_from_string($fileinfo, $file_string);
        return $this;
    }

    /**
     * @param string $filepath
     * @return \Moodledata
     */
    public function copyFile($params) {
        $this->controlParamArray($params);
        $source = new stdClass();
        $source->source = $this->file->get_source();
        $moodledata = new Moodledata($this->db);

        // Prepare file record object
        $fileinfo = array(
            'contextid' => (!empty($params['contextid']) ? $params['contextid'] : $this->file->get_contextid()),
            'component' => (!empty($params['component']) ? $params['component'] : $this->file->get_component()),
            'filearea' => (!empty($params['filearea']) ? $params['filearea'] : $this->file->get_filearea()),
            'itemid' => (!empty($params['itemid']) ? $params['itemid'] : $this->file->get_itemid()),
            'filepath' => (!empty($params['filepath']) ? $params['filepath'] : $this->file->get_filepath()),
            'filename' => (!empty($params['filename']) ? $params['filename'] : $this->file->get_filename()),
            'source' => (!empty($params['source']) ? $params['source'] : serialize($source)),
            'license' => (!empty($params['license']) ? $params['license'] : $this->file->get_license()),
        );

        if (!$moodledata->fileExists($fileinfo)) {
            try {
                $this->file_storage->create_file_from_storedfile($fileinfo, $this->file->get_id());
                $moodledata->loadFile($fileinfo)->actualizeFile();
            } catch (Exception $exc) {
                throw new Exception('File didnt copy! (' . serialize($params) . ')');
            }
        } else {
            throw new Exception('Copy of file already exists! (' . serialize($params) . ')');
        }
        return $this;
    }

    /**
     * @param array $params
     * @param string $order_by
     * @param string $group_by
     * @param string $limit
     * @return boolean
     */
    public function fileExists($params, $order_by = null, $group_by = null, $limit = null) {
        $moodledata = new Moodledata($this->db);
        return $moodledata->setLoadReturnInfo(true)->loadFile($params, $order_by, $group_by, $limit);
    }

    /**
     * @return \Moodledata
     * @throws Exception
     */
    public function delete() {
        $this->isFileLoaded()->file->delete();
        $this->unsetFile()->unsetParams();
        return $this;
    }

    /*     * *********************************************************************** */
    /* private methods */
    /*     * *********************************************************************** */

    /**
     * @param boolean $load_return_info
     * @return \Moodledata
     */
    private function setLoadReturnInfo($load_return_info) {
        $this->load_return_info = ($load_return_info ? true : false);
        return $this;
    }

    /**
     * @return \Moodledata
     */
    private function setTimeModified() {
        $this->isFileLoaded()->file->set_timemodified(time());
        return $this;
    }

    private function setLicense() {
        if (!$this->file->get_license()) {
            $this->isFileLoaded()->file->set_license('unknown');
        }
        return $this;
    }

    private function setAuthor() {
        if (!$this->file->get_author()) {
            $this->isFileLoaded()->file->set_author('');
        }
        return $this;
    }

    /**
     * mozna pribudou dalsi operace nad souborem, proto je to zatim jen alias...
     * @return \Moodledata
     */
    private function actualizeFile() {
        $this
                ->setTimeModified()
                ->setLicense()
                ->setAuthor();
        // aktualizuje i posledni instanci daneho souboru
        $moodledata = new Moodledata($this->db);
        $moodledata->loadFile(array('filename' => $this->file->get_filename()), 'id DESC', null, 1);
        if ($this->file->get_id() !== $moodledata->getId()) {
            $moodledata->actualizeFile();
        }
        return $this;
    }

    /**
     * @param string $order_by
     * @return \Moodledata
     */
    public function setOrderBy($order_by) {
        $this->order_by = $order_by;
        return $this;
    }

    /**
     * @param string $group_by
     * @return \Moodledata
     */
    public function setGroupBy($group_by) {
        $this->group_by = $group_by;
        return $this;
    }

    /**
     * @param string $limit
     * @return \Moodledata
     */
    public function setLimit($limit) {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @return \Moodledata
     * @throws Exception
     */
    private function isFileLoaded() {
        if (empty($this->file)) {
            throw new Exception('File isnt loaded!');
        }
        return $this;
    }

    /**
     * @return \Moodledata
     * @throws Exception
     */
    private function isTempCreated() {
        if (empty($this->temp_moodle_file)) {
            throw new Exception('Temporary file doesnt exists!');
        }
        return $this;
    }

    /**
     * @param string $content
     * @param array $params
     * @return \Moodledata
     * @throws Exception
     */
    private function createMoodleTempFile($content = false) {
        if ($this->temp_file_name) {
            $content = ($content ? $content : $this->getFileContent());
            // Prepare file record object
            $fileinfo = array(
                'contextid' => $this->file->get_contextid(),
                'contextid' => $this->file->get_contextid(),
                'component' => $this->file->get_component(),
                'filearea' => $this->file->get_filearea(),
                'itemid' => $this->file->get_itemid(),
                'filepath' => $this->file->get_filepath(),
                'filename' => $this->temp_file_name);
            $this->file_storage->create_file_from_string($fileinfo, $content);
            $this->temp_moodle_file = $this->file_storage->get_file($fileinfo['contextid'], $fileinfo['component'], $fileinfo['filearea'], $fileinfo['itemid'], $fileinfo['filepath'], $fileinfo['filename']);
        } else {
            throw new Exception('Name of temporary file doesnt set!');
        }
        return $this;
    }

    /**
     * @return \Moodledata
     */
    private function copyContentToMoodleTempFile() {
        $this
                ->isTempCreated()
        ->file->copy_content_to($this->temp_moodle_file->get_filepath() . $this->temp_moodle_file->get_filename());
        return $this;
    }

    /**
     * @return \Moodledata
     * @throws Exception
     */
    private function replaceContentByMoodleTempFile() {
        if ($this->temp_moodle_file) {
            $this->file->replace_content_with($this->temp_moodle_file);
        } else {
            throw new Exception('Moodle temporary file doesnt set!');
        }
        return $this;
    }

    /**
     * @return \Moodledata
     * @throws Exception
     */
    private function deleteMoodleTempFile() {
        $this->isTempCreated()->temp_moodle_file->delete();
        $this->unsetTemp();
        return $this;
    }

    /**
     * @return \Moodledata
     */
    private function setTempFileName($stay_original = false) {
        $filename = $this->file->get_filename();
        $filename_arr = explode('.', $filename);
        $this->temp_file_name = ($stay_original ? $filename : reset($filename_arr) . '_' . date('Y-m-d_H-i-s') . '.' . end($filename_arr));
        return $this;
    }

    /**
     * @param string $content
     * @return \Moodledata
     */
    private function createTempFile($content) {
        make_temp_directory($this->temp_dir_name);
        $temp = $this->temp_dir . '/' . $this->temp_file_name;
        $fp = fopen($temp, 'w');
        fputs($fp, $content);
        fclose($fp);
        return $this;
    }

    /**
     * @return \Moodledata
     */
    private function deleteTempFile() {
        unlink($this->temp_dir . '/' . $this->temp_file_name);
        return $this;
    }

    /**
     * @return \Moodledata
     */
    private function setFiles() {
        $conditions = array();
        foreach ($this->params as $param) {
            if ($this->$param) {
                if ($param === 'license' && ($this->$param === 'unknown' || !$this->$param)) {
                    $conditions[] = "(" . $param . "='unknown' OR " . $param . " IS NULL)";
                } else {
                    $conditions[] = $param . "='" . $this->$param . "'";
                }
            }
        }
        $query = "
			SELECT
			id,contextid,component,filearea,itemid,filepath,filename
			FROM {files}
			WHERE
			" . implode(' AND ', $conditions) . "
			" . ($this->group_by ? "GROUP BY " . $this->group_by : false) . "
			" . ($this->order_by ? "ORDER BY " . $this->order_by : false) . "
			" . ($this->limit ? "LIMIT " . $this->limit : false) . "
			";

        $records = $this->db->get_records_sql($query);
        if ($records) {
            foreach ($records as $record) {
                $this->files[] = $this->file_storage->get_file($record->contextid, $record->component, $record->filearea, $record->itemid, $record->filepath, $record->filename);
            }
        }
        return $this;
    }

    /**
     * aby nedoslo ke kolizim pri loadu noveho souboru
     * @return \Moodledata
     */
    private function reset() {
        $this
                ->unsetParams()
                ->unsetFile()
                ->unsetTemp()
                ->unsetDb();
        return $this;
    }

    /**
     * @return \Moodledata
     */
    private function unsetDb() {
        $this->order_by = null;
        $this->group_by = null;
        $this->limit = null;
        return $this;
    }

    /**
     * @return \Moodledata
     */
    private function unsetTemp() {
        $this->temp_file_name = null;
        $this->temp_moodle_file = null;
        return $this;
    }

    /**
     * @return \Moodledata
     */
    private function unsetFile() {
        $this->file = null;
        $this->files = null;
        return $this;
    }

    /**
     * @return \Moodledata
     */
    private function unsetParams() {
        foreach ($this->params as $param) {
            $this->$param = null;
        }
        return $this;
    }

    /**
     *
     * @param array $params ('id'=>'','contenthash'=>'','pathnamehash'=>'','contextid'=>'','component'=>'','filearea'=>'','itemid'=>'','filepath'=>'','filename'=>'','userid'=>'')
     * @return \Moodledata
     * @throws Exception
     */
    private function setParams($params) {
        $this->controlParamArray($params);
        foreach ($params as $key => $value) {
            $this->$key = $value;
        }
        return $this;
    }

    /**
     * @param array $param_array
     * @return \Moodledata
     * @throws Exception
     */
    private function controlParamArray($param_array) {
        if (!empty($param_array)) {
            if (is_array($param_array)) {
                foreach ($param_array as $param_name => $value) {
                    $value = $value;
                    $this->controlParamName($param_name);
                }
            } else {
                throw new Exception('Controled params must be in array!');
            }
        } else {
            throw new Exception('Params array must not be empty!');
        }
        return $this;
    }

    /**
     * @param string $param_name
     * @return \Moodledata
     * @throws Exception
     */
    private function controlParamName($param_name) {
        if (!in_array($param_name, $this->params)) {
            throw new Exception('Unknown param name "' . $param_name . '"!');
        }
        return $this;
    }

}