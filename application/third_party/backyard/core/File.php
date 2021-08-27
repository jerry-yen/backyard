<?php

/**
 * 後花園 - 檔案處理
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */

namespace backyard\core;

class File extends \backyard\core\Item
{

    /**
     * 建構子
     */
    public function __construct($table = 'file', $data = array())
    {
        $this->data = $data;
        $this->table = $table;
    }

    /**
     * 取得多筆檔案
     * @param string $table 資料表名稱
     * @param array $where 搜尋條件
     * @param array $sort 排序條件
     * @param array $fields 取得指定欄位
     */
    public function list($table, $where = array(), $sort = array(), $fields = array())
    {
        $responses = parent::_list($table, $where, $sort, $fields);
        $items = array();
        foreach ($responses as $response) {
            $item = new \backyard\core\File($table, $response);
            $items[] = $item->toArray();
        }
        return $items;
    }

    /**
     * 上傳檔案
     * 
     * @param string $code 模組代碼
     */
    public function upload($inputs = array())
    {
        if ($inputs['code'] == '') {
            return array('status' => 'failed', 'message' => '尚未設定模組代碼');
        }

        if ($inputs['field'] == '') {
            return array('status' => 'failed', 'message' => '尚未設定上傳欄位');
        }

        // 取得 Dataset 設定
        $response = get_instance()->backyard->dataset->get($inputs['code'], 'dataset', true);

        if ($response == array()) {
            return $response;
        }
        foreach ($response['fields'] as $key => $field) {
            $response['fields'][$field['frontendVariable']] = $field;
            unset($response['fields'][$key]);
        }

        // 取得上傳欄位設定
        $field = $response['fields'][$inputs['field']];

        // 取得檔案副檔名
        $ext = substr(strrchr($_FILES[$inputs['field']]['name'], '.'), 1);

        // 取得上傳目錄
        get_instance()->backyard->config->loadConfigFile('file');
        $temporaryDir = get_instance()->backyard->config->getConfig('file')['temporary_dir'];

        // 上傳Library設定
        get_instance()->load->library('upload', array(
            'upload_path'   => $temporaryDir,
            'allowed_types' => '*',
            'max_size'      => 100000000000,
            'file_name'     => uniqid('', true) . '.' . $ext
        ));

        // 上傳
        if (!get_instance()->upload->do_upload($inputs['field'])) {
            return array('status' => 'failed', 'message' => get_instance()->upload->display_errors());
        }

        // 取得相對路徑
        $file = get_instance()->upload->data();
        $file['short_path'] = str_replace($temporaryDir, '', $file['full_path']);
        $file['created_at'] = date('Y-m-d H:i:s');

        get_instance()->backyard->loadLibrary('Code');
        return array('status' => 'success', 'file' => array(
            'id'        => get_instance()->backyard->code->getGUID(),
            'name'      => $file['client_name'],
            'ext'       => $file['file_ext'],
            'file_type' => $file['file_type'],
            'path'      => $file['short_path'],
            'file_size' => $file['file_size'],
            'created_at' => date('Y-m-d H:i:s'),
        ));
    }

    public function moveTemporaryToUploadDirectory()
    {
        // 取得上傳目錄
        get_instance()->backyard->config->loadConfigFile('file');
        $baseUploadDir = get_instance()->backyard->config->getConfig('file')['upload_dir'];
        $temporaryDir = get_instance()->backyard->config->getConfig('file')['temporary_dir'];

        $temporary = $temporaryDir . $this->data['path'];
        $upload = $baseUploadDir . $this->data['path'];
        rename($temporary, $upload);
    }

    public function insert($table = '', $file = array())
    {
        if ($table == '') {
            $table = $this->table;
        }

        if ($file == array()) {
            $file = $this->data;
        }

        $file['created_at'] = (!isset($file['created_at']) || $file['created_at'] == '') ? date('Y-m-d H:i:s') : $file['created_at'];
        $file['updated_at'] = (!isset($file['updated_at']) || $file['updated_at'] == '') ? date('Y-m-d H:i:s') : $file['updated_at'];

        $insert_id = parent::_insert($table, $file);
        return $insert_id;
    }

    public function update($table = '', $file = array(), $skipValidation = false)
    {
        if ($table == '') {
            $table = $this->table;
        }

        if ($file == array()) {
            $file = $this->data;
        }

        parent::_update($table, $file, array('id' => $file['id']));
    }
    
    public function delete($table, $data = array()){

        parent::_delete($table, array('id' => $data['id']));

        get_instance()->backyard->config->loadConfigFile('file');
        $baseUploadDir = get_instance()->backyard->config->getConfig('file')['upload_dir'];
        if(file_exists($baseUploadDir . $data['path'])){
            unlink($baseUploadDir . $data['path']);
            return array('status' => 'success');
        }

        return array('status' => 'success', 'message' => '檔案不存在');
    }
    
}
