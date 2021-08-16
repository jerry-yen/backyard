<?php

/**
 * 後花園 - 資料處理
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */

namespace backyard\core;

class Data extends \backyard\Package
{
    /**
     * @var 資料庫物件
     */
    private $database = null;

    /**
     * 建構子
     */
    public function __construct(&$backyard)
    {
        parent::__construct($backyard);
        $this->connection();
    }

    public function connection($connectionName = 'default')
    {
        // 從設定檔取得資料庫連線
        $this->backyard->config->loadConfigFile('database');
        $connectionConfigs = $this->backyard->config->getConfig('database');
        $connectionConfig = isset($connectionConfigs[$connectionName]) ?
            $connectionConfigs[$connectionName] :
            $connectionConfigs['default'];

        get_instance()->load->database($connectionConfig);
        $this->database = get_instance()->db;
    }

    /**
     * 基本資料表安裝
     * 
     * @return null
     */
    public function install()
    {
        // 從設定檔取得建置基本資料表的SQL語法
        $this->backyard->config->loadConfigFile('install');
        $sqls = $this->backyard->config->getConfig('install');
        foreach ($sqls as $key => $sql) {
            $this->database->query($sql);
        }
    }

    /**
     * 新增資料表
     * 
     * @param string $code 模組代碼(資料表名稱)
     * @param array $fields 欄位資訊
     */
    public function createTable($inputs = array())
    {
        $code = $inputs['_code'];
        $fields = json_decode($inputs['fields'], true);

        // 表單名稱
        $tableName = $this->database->dbprefix . $code;

        // 新增表單，並設定內建的基本欄位
        $sql = 'CREATE TABLE IF NOT EXISTS ' . $tableName . '(
                    id VARCHAR(40) NOT NULL PRIMARY KEY COMMENT "識別碼" COLLATE utf8_unicode_ci,
                    parent_id VARCHAR(40) NOT NULL COMMENT "上層識別碼" COLLATE utf8_unicode_ci,
                    domain_id VARCHAR(40) NULL COMMENT "網域識別碼" COLLATE utf8_unicode_ci,
                    member_id VARCHAR(40) NULL COMMENT "使用者識別碼" COLLATE utf8_unicode_ci,
                    visibility INT(11) NULL COMMENT "可見度：0:公開,1:私人,2:上級可看" COLLATE utf8_unicode_ci,
                    level INT(11) NULL COMMENT "層數(分類使用)" COLLATE utf8_unicode_ci,
                    created_at DATETIME COMMENT "建置時間" COLLATE utf8_unicode_ci,
                    updated_at DATETIME COMMENT "更新時間" COLLATE utf8_unicode_ci,
                    sorted_at DATETIME NULL COMMENT "排序時間" COLLATE utf8_unicode_ci,
                    sequence INT(11) NULL COMMENT "排列順序" COLLATE utf8_unicode_ci,
                    top_at DATETIME NULL COMMENT "置頂時間" COLLATE utf8_unicode_ci,
                    KEY `parent_id_index` (`parent_id`),
                    KEY `domain_id_index` (`domain_id`),
                    KEY `member_id_index` (`member_id`),
                    KEY `created_at_index` (`created_at`),
                    KEY `updated_at_index` (`updated_at`),
                    KEY `sorted_at_index` (`sorted_at`),
                    KEY `sequence_index` (`sequence`),
                    KEY `top_at_index` (`top_at`)
                ) CHARACTER SET utf8 COLLATE utf8_unicode_ci;
        ';

        $this->database->query($sql);

        $defualtFields = array();
        $defualtFields['id'] = true;
        $defualtFields['parent_id'] = true;
        $defualtFields['domain_id'] = true;
        $defualtFields['member_id'] = true;
        $defualtFields['visibility'] = true;
        $defualtFields['level'] = true;
        $defualtFields['created_at'] = true;
        $defualtFields['updated_at'] = true;
        $defualtFields['sorted_at'] = true;
        $defualtFields['sequence'] = true;
        $defualtFields['top_at'] = true;

        // 抓出這個表單的所有欄位
        $dbFields = $this->database->list_fields($tableName);
        foreach ($dbFields as $key => $field) {
            $dbFields[$field] = $field;
            unset($dbFields[$key]);
        }


        // 刪除不在這次設定的欄位
        foreach ($fields as $key => $field) {
            $fieldType = 'varchar(10)';
            switch ($field['component']) {
                case 'slider':
                case 'number':
                    $fieldType = 'int(10)';
                    break;
                case 'checkbox':
                case 'select':
                case 'text':
                    $fieldType = 'varchar(100)';
                    break;
                case 'textarea':
                    $fieldType = 'varchar(255)';
                    break;
                case 'switch':
                    $fieldType = 'varchar(1)';
                    break;
                case 'text':
                    $fieldType = 'varchar(100)';
                    break;
                case 'date':
                    $fieldType = 'date';
                    break;
                case 'htmleditor':
                    $fieldType = 'text';
                    break;
                default:
                    $fieldType = 'varchar(100)';
            }

            $isNull = true;
            foreach ($field['validator'] as $validator) {
                if ($validator == 'required') {
                    $isNull = false;
                    break;
                }
            }

            // 新增
            if (!isset($dbFields[$field['dbVariable']]) && !isset($defualtFields[$field['dbVariable']])) {
                $sql = 'ALTER TABLE ' . $tableName . ' ADD COLUMN ' . $field['dbVariable'] . ' ' . $fieldType . ((!$isNull) ? ' NOT' : '') . ' NULL COMMENT "' . $field['name'] . '";';
            }
            // 修改
            else if (isset($dbFields[$field['dbVariable']])) {
                $sql = 'ALTER TABLE ' . $tableName . ' MODIFY COLUMN ' . $field['dbVariable'] . ' ' . $fieldType . ((!$isNull) ? ' NOT' : '') . ' NULL COMMENT "' . $field['name'] . '";';
            }

            $this->database->query($sql);

            $fields[$field['dbVariable']] = $field;
        }

        foreach ($dbFields as $field => $value) {
            if (!isset($fields[$field]) && !isset($defualtFields[$field])) {
                $sql = 'ALTER TABLE ' . $tableName . ' DROP COLUMN ' . $field . ';';
                $this->database->query($sql);
            }
        }
    }

    /**
     * 取得多筆資料記錄
     * @param int $count 顯示筆數
     * @param boolean $pagination 是否分頁
     * 
     * @return array
     */
    public function getItems($inputs = array(), $sort = array(), $pagination = false, $count = 10)
    {
        if (!isset($inputs['code'])) {
            return array('status' => 'failed', 'message' => '尚未設定模組代碼');
        }

        $response = $this->backyard->getUser()->convertToWhere($inputs);
        $where = $response['where'];


        /*
         * 搜尋條件要過濾掉資料表中沒有的欄位
         */

        // 取得資料表中的所有欄位
        $tableFields = $this->database->list_fields($response['table']);
        foreach ($tableFields as $key => $field) {
            $tableFields[$field] = true;
            unset($tableFields[$key]);
        }

        // 過濾要取得的欄位
        if (isset($fields) && count($fields) > 0) {
            foreach ($fields as $key => $value) {
                if (!isset($tableFields[$value])) {
                    unset($fields[$key]);
                }
            }
        } else {
            $fields = array();
        }

        // 過濾要搜尋的條件
        if (isset($where) && count($where) > 0) {
            foreach ($where as $key => $value) {
                if (!isset($tableFields[$key])) {
                    unset($where[$key]);
                }
            }
        } else {
            $where = array();
        }
        // 欄位
        $this->database = $this->database->select((count($fields) == 0) ? '*' : (implode(',', $fields)));

        // 表單
        $this->database = $this->database->from($response['table']);

        // 條件
        $this->database = $this->database->where($where);

        // 排序
        if (!isset($sort) || !is_array($sort) || count($sort) == 0) {
            $sort = array(
                'top_at' => 'DESC',
                'sorted_at' => 'ASC',
                'sequence' => 'ASC',
                'created_at' => 'DESC',
                'updated_at' => 'DESC',
            );
        }


        foreach ($sort as $key => $method) {
            if (!isset($tableFields[$key])) {
                continue;
            }
            $this->database->order_by($key, $method);
        }


        // 取得總筆數
        $total = $this->database->count_all_results('', false);


        // 分頁處理
        $totalPage = 1;
        $current_page = 1;
        if ($pagination) {
            $inputPage = get_instance()->input->get('page');
            $page = isset($inputPage) ?
                get_instance()->input->get('page') :
                get_instance()->input->post('page');

            $inputCount = get_instance()->input->get('count');
            $inputCount = isset($inputCount) ?
                get_instance()->input->get('count') :
                get_instance()->input->post('count');
            $count = isset($inputCount) ? $inputCount : $count;
            if ($count > $total || $count == -1) {
                $count = $total;
            }
            $count = ($count == 0) ? 10 : $count;
            $totalPage = ceil($total / $count);
            $totalPage = ($totalPage == 0) ? 1 : $totalPage;
            $page = isset($page) ? $page : 1;
            $page = ($page < 1) ? 1 : $page;
            $page = ($page > $totalPage) ? $totalPage : $page;

            $offset = ($page - 1) * $count;
            $this->database = $this->database->limit($count, $offset);

            $current_page = (int)(isset($page) ? $page : 1);
        }

        // 取得結果
        $results = $this->database->get()->result_array();

        // 取得資料集後設資訊

        $dataset = $this->backyard->dataset->getItem($inputs['code']);
        if ($dataset['status'] == 'nodata') {
            foreach ($results as $key => $result) {
                $results[$key] = $this->backyard->getUser()->convertToData($result);
            }
        } else {
            foreach ($results as $key => $result) {
                $results[$key] = $this->backyard->getUser()->convertToData($result);
                $results[$key] = $this->backyard->converter->checkOutputs($dataset['dataset'], $results[$key]);
            }
        }

        return array(
            'status' => 'success',
            'total' => $total,
            'total_page' => $totalPage,
            'current_page' => $current_page,
            'results' => $results
        );
    }

    /**
     * 更新多筆資料記錄
     * 
     * @return array
     */
    public function updateItems($inputs = array())
    {
        foreach ($inputs['condition'] as $key => $input) {
            $data = array(
                'code' => $inputs['code'],
                'id'   => $input,
            );

            foreach ($inputs['value'][$key] as $fieldName => $value) {
                $data[$fieldName] = $value;
            }

            $response = $this->updateItem($data, true);
            if ($response['status'] != 'success') {
                return $response;
            }
        }

        return array('status' => 'success');
    }

    /**
     * 取得單筆資料記錄
     * @param string $code 模組代碼
     * @param array $fields 指定欄位
     * @param array $where 搜尋條件
     * 
     * @return array
     */
    public function getItem($inputs = array())
    {
        /*
        if (!isset($inputs['code'])) {
            return array('status' => 'failed', 'message' => '尚未設定模組代碼');
        }
        */
        $response = $this->backyard->getUser()->convertToWhere($inputs);
        $where = $response['where'];
        /*
         * 搜尋條件要過濾掉資料表中沒有的欄位
         */

        // 取得資料表中的所有欄位
        $tableFields = $this->database->list_fields($response['table']);
        foreach ($tableFields as $key => $field) {
            $tableFields[$field] = true;
            unset($tableFields[$key]);
        }
        // 過濾要取得的欄位
        if (isset($fields) && count($fields) > 0) {
            foreach ($fields as $key => $value) {
                if (!isset($tableFields[$value])) {
                    unset($fields[$key]);
                }
            }
        } else {
            $fields = array();
        }
        // 過濾要搜尋的條件
        if (isset($where) && count($where) > 0) {
            foreach ($where as $key => $value) {
                if (!isset($tableFields[$key])) {
                    unset($where[$key]);
                }
            }
        } else {
            $where = array();
        }

        // 欄位
        $this->database = $this->database->select((count($fields) == 0) ? '*' : (implode(',', $fields)));

        // 表單
        $this->database = $this->database->from($response['table']);

        // 條件
        $this->database = $this->database->where($where);

        // 取得結果
        $item = $this->database->get()->row_array();

        // 根據不同使用者，進行資料格式的轉換
        $item = $this->backyard->getUser()->convertToData($item);
        if (is_null($item)) {
            return array('status' => 'success', 'item' => $item);
        }


        // 特殊欄位，例如檔案、一對多欄位、多對多欄位處理

        // 如有 config_type 欄位，代表為開發者，不會有特殊欄位處理的需求
        if (isset($inputs['config_type'])) {
            return array('status' => 'success', 'item' => $item);
        }
        // 取得 Dataset 後設資料分析欄位資訊
        $response = $this->backyard->dataset->getItem($inputs['code']);
        if ($response['status'] == 'failed') {
            return array('status' => 'success', 'item' => $item);
        }

        $item = $this->backyard->converter->checkOutputs($response['dataset'], $item);

        foreach ($response['dataset']['fields'] as $field) {
            $parts = explode(';', $field['source']);
            // 檔案元件
            if (in_array('file', $parts)) {
                $item[$field['dbVariable']] = $this->getFiles($item['id'], $field['dbVariable']);
            }

            // 多對多元件
            if (in_array('relation', $parts)) {
                // 未處理
            }

            // 一對多元件
            if (preg_match('/subitem\{(.*?)\};/i', $field['source'], $res)) {
                $item[$field['dbVariable']] = $this->getSubItems($item['id'], $res[1]);
            }
        }


        return array('status' => 'success', 'item' => $item);
    }

    /**
     * 新增記錄
     * 
     * @param array $exValues 額外處理過的值
     * 
     * @param string GUID 新增記錄的ID
     */
    public function insertItem($inputs = array(), $code = null)
    {
        $tmpFiles = array();
        $tmpRelations = array();
        $tmpSubItems = array();

        if (isset($code) && !is_null($code)) {
            $inputs['code'] = $code;
        } else {
            if (!isset($inputs['code'])) {
                return array('status' => 'failed', 'message' => '尚未設定模組代碼');
            }

            $response = $this->backyard->dataset->getItem($inputs['code']);
            if ($response['status'] == 'failed') {
                return $response;
            }

            // 取得多對多選項及檔案類型的欄位，資料庫要另外存
            foreach ($response['dataset']['fields'] as $field) {
                $parts = explode(';', $field['source']);
                // 檔案元件
                if (in_array('file', $parts)) {
                    if (isset($tmpFiles[$field['dbVariable']])) {
                        $tmpFiles[$field['dbVariable']] = array();
                    }
                    $tmpFiles[$field['dbVariable']] = (isset($inputs[$field['dbVariable']])) ? json_decode($inputs[$field['dbVariable']], true) : array();
                    $inputs[$field['dbVariable']] = '';
                }

                // 多對多元件
                if (in_array('relation', $parts)) {
                    if (isset($tmpRelations[$field['dbVariable']])) {
                        $tmpRelations[$field['dbVariable']] = array();
                    }
                    $tmpRelations[$field['dbVariable']] = (isset($inputs[$field['dbVariable']])) ? json_decode($inputs[$field['dbVariable']], true) : array();
                    $inputs[$field['dbVariable']] = '';
                }

                // 一對多元件
                if (preg_match('/subitem\{(.*?)\};/i', $field['source'], $res)) {
                    if (!isset($tmpSubItems[$field['dbVariable']])) {
                        $tmpSubItems[$field['dbVariable']] = array();
                    }
                    $tmpSubItems[$field['dbVariable']]['code'] = $res[1];
                    $tmpSubItems[$field['dbVariable']]['data'] = (isset($inputs[$field['dbVariable']])) ? json_decode($inputs[$field['dbVariable']], true) : array();
                    $inputs[$field['dbVariable']] = '';
                }
            }

            // 驗證輸入參數
            $response = $this->backyard->validator->checkInputs($response['dataset'], $inputs);
            if ($response['status'] == 'failed') {
                return $response;
            }
            $inputs = $response['fields'];
        }



        // 預設ID
        if (!isset($inputs['id'])) {
            $this->backyard->loadLibrary('Code');
            $inputs['id'] = $this->backyard->code->getGUID();
        }

        // 預設建置時間
        if (!isset($inputs['created_at'])) {
            $inputs['created_at'] = date('Y-m-d H:i:s');
        }

        // 預設更新時間
        if (!isset($inputs['updated_at'])) {
            $inputs['updated_at'] = date('Y-m-d H:i:s');
        }

        $response = $this->backyard->getUser()->convertToDatabase($inputs);

        // 新增記錄
        $this->database->insert($response['table'], $response['value']);

        // 檔案元件處理
        foreach ($tmpFiles as $fieldName => $files) {
            $this->saveFiles($inputs['id'], $fieldName, $files);
        }

        // 一對多元件處理
        foreach ($tmpSubItems as $fieldName => $subitems) {
            $this->saveSubItems($inputs['id'], $subitems['code'], $subitems['data']);
        }
        return array('status' => 'success', 'id' => $inputs['id']);
    }

    /**
     * 更新記錄
     * 
     * @param array $exValues 額外處理過的值
     * 
     * @param string GUID 更新記錄的ID
     */
    public function updateItem($inputs = array(), $ignoreValidation = false, $code = null)
    {
        $tmpFiles = array();
        $tmpRelations = array();
        $tmpSubItems = array();
        if (isset($code) && !is_null($code)) {
            $inputs['code'] = $code;
        } else {
            if (!isset($inputs['code'])) {
                return array('status' => 'failed', 'message' => '尚未設定模組代碼');
            }

            if (!isset($inputs['id']) || is_null($inputs['id'])) {
                return array('status' => 'failed', 'message' => '更新資料表記錄:缺少識別碼');
            }

            $response = $this->backyard->dataset->getItem($inputs['code']);
            if ($response['status'] == 'failed') {
                return $response;
            }

            // 取得多對多選項及檔案類型的欄位，資料庫要另外存
            foreach ($response['dataset']['fields'] as $field) {
                $parts = explode(';', $field['source']);
                // 檔案元件
                if (in_array('file', $parts)) {
                    if (isset($tmpFiles[$field['dbVariable']])) {
                        $tmpFiles[$field['dbVariable']] = array();
                    }
                    $tmpFiles[$field['dbVariable']] = (isset($inputs[$field['dbVariable']])) ? json_decode($inputs[$field['dbVariable']], true) : array();
                    $inputs[$field['dbVariable']] = '';
                }

                // 多對多元件
                if (in_array('relation', $parts)) {
                    if (isset($tmpRelations[$field['dbVariable']])) {
                        $tmpRelations[$field['dbVariable']] = array();
                    }
                    $tmpRelations[$field['dbVariable']] = (isset($inputs[$field['dbVariable']])) ? json_decode($inputs[$field['dbVariable']], true) : array();
                    $inputs[$field['dbVariable']] = '';
                }

                // 一對多元件
                if (preg_match('/subitem\{(.*?)\};/i', $field['source'], $res)) {
                    if (!isset($tmpSubItems[$field['dbVariable']])) {
                        $tmpSubItems[$field['dbVariable']] = array();
                    }
                    $tmpSubItems[$field['dbVariable']]['code'] = $res[1];
                    $tmpSubItems[$field['dbVariable']]['data'] = (isset($inputs[$field['dbVariable']])) ? json_decode($inputs[$field['dbVariable']], true) : array();
                    $inputs[$field['dbVariable']] = '';
                }
            }
            if (!$ignoreValidation) {
                // 驗證輸入參數
                $response = $this->backyard->validator->checkInputs($response['dataset'], $inputs);
                if ($response['status'] == 'failed') {
                    return $response;
                }

                $inputs = $response['fields'];
            }
        }



        $response = $this->backyard->getUser()->convertToDatabase($inputs);
        $value = $response['value'];

        // 預設更新時間，無論如何，只要有更新資料就要更新時間，如有指定更新時間的需求，請另開欄位
        $value['updated_at'] = date('Y-m-d H:i:s');

        // 更新記錄
        $this->database->where(array('id' => $value['id']));
        $this->database->update($response['table'], $value);

        // 檔案元件處理
        foreach ($tmpFiles as $fieldName => $files) {
            $this->saveFiles($value['id'], $fieldName, $files);
        }

        // 一對多元件處理
        foreach ($tmpSubItems as $fieldName => $subitems) {
            $this->saveSubItems($value['id'], $subitems['code'], $subitems['data']);
        }



        return array('status' => 'success', 'id' => $value['id']);
    }

    /**
     * 刪除記錄
     */
    public function deleteItem($inputs = array())
    {
        if (!isset($inputs['code'])) {
            return array('status' => 'failed', 'message' => '尚未設定模組代碼');
        }

        if (!isset($inputs['id']) || is_null($inputs['id'])) {
            return array('status' => 'failed', 'message' => '刪除資料表記錄:缺少識別碼');
        }

        $response = $this->backyard->getUser()->convertToDatabase($inputs);
        $value = $response['value'];

        // 刪除記錄
        $this->database->where('id', $value['id']);
        $this->database->delete($response['table']);




        // 取得 Dataset 後設資料分析欄位資訊
        $response = $this->backyard->dataset->getItem($inputs['code']);
        if ($response['status'] == 'failed') {
            return array('status' => 'success');
        }

        foreach ($response['dataset']['fields'] as $field) {
            $parts = explode(';', $field['source']);
            // 檔案元件
            if (in_array('file', $parts)) {
                $this->deleteFiles($value['id'], $field['dbVariable']);
            }

            // 多對多元件
            if (in_array('relation', $parts)) {
                // 未處理
            }

            // 一對多元件
            if (preg_match('/subitem\{(.*?)\};/i', $field['source'], $res)) {
                $this->deleteSubItems($value['id'], $res[1]);
            }
        }

        return array('status' => 'success');
    }

    public function getFiles($itemId, $fieldName = '')
    {
        $table = get_instance()->db->dbprefix . 'file';
        $this->database = $this->database->select('id,own_field,name,ext,file_type,path,file_size,created_at,sorted_at,sequence');
        $this->database = $this->database->from($table);

        $where = array('own_id' => $itemId);
        if ($fieldName != '') {
            $where['own_field'] = $fieldName;
        }
        $this->database->where($where);
        $this->database->order_by('sorted_at', 'ASC');
        $this->database->order_by('sequence', 'ASC');
        $this->database->order_by('created_at', 'DESC');
        $files = $this->database->get()->result_array();
        return $files;
    }

    public function saveFiles($itemId, $fieldName, $files)
    {

        if (!is_array($files)) {
            return;
        }
        $this->backyard->loadLibrary('Code');

        // 原本就存在的檔案
        $oriFiles = $this->getFiles($itemId, $fieldName);
        foreach ($oriFiles as $key => $file) {
            $oriFiles[$file['id']] = $file;
            unset($oriFiles[$key]);
        }

        $table = get_instance()->db->dbprefix . 'file';
        foreach ($files as $sequence => $file) {

            // 存在於原本資料庫中的檔案
            if (isset($oriFiles[$file['id']])) {
                $this->database->where('id', $file['id']);
                $this->database->update($table, array(
                    'sorted_at' => date('Y-m-d H:i:s'),
                    'sequence' => $sequence
                ));
            } else {

                // 將檔案從暫存資料夾移至正式上傳資料夾
                $this->backyard->file->moveTemporaryToUploadDirectory($file['path']);

                // 將檔案資訊儲存至資料庫
                $created_at = date('Y-m-d H:i:s');
                $this->database->insert($table, array(
                    'id'        => $file['id'],
                    'own_id' => $itemId,
                    'own_field' => $fieldName,
                    'name'      => $file['name'],
                    'ext'       => $file['ext'],
                    'file_type' => $file['file_type'],
                    'path'      => $file['path'],
                    'file_size' => $file['file_size'],
                    'created_at' => $created_at,
                    'updated_at' => $created_at,
                    'sorted_at' => $created_at,
                    'sequence' => $sequence,
                ));
            }

            if (isset($file['id']) && isset($oriFiles[$file['id']])) {
                unset($oriFiles[$file['id']]);
            }
        }

        // 沒被新增或更新的檔案，刪除
        foreach ($oriFiles as $file) {
            $response = $this->backyard->file->delete($file);
            if ($response['status'] == 'success') {
                // 刪除記錄
                $this->database->where('id', $file['id']);
                $this->database->delete($table);
            }
        }
    }

    public function deleteFiles($itemId, $fieldName)
    {
        $files = $this->getFiles($itemId, $fieldName);
        foreach ($files as $file) {
            $this->backyard->file->delete($file);
        }
    }


    public function getSubItems($itemId, $code = '')
    {
        $table = get_instance()->db->dbprefix . $code;
        $this->database = $this->database->select('*');
        $this->database = $this->database->from($table);

        $where = array('parent_id' => $itemId);
        $this->database->where($where);
        $this->database->order_by('sorted_at', 'ASC');
        $this->database->order_by('sequence', 'ASC');
        $this->database->order_by('created_at', 'DESC');
        $items = $this->database->get()->result_array();
        return $items;
    }

    public function saveSubItems($itemId, $code, $items)
    {
        if (!is_array($items)) {
            return;
        }

        $this->backyard->loadLibrary('Code');

        // 原本就存在的子項目
        $oriSubItems = $this->getSubItems($itemId, $code);
        foreach ($oriSubItems as $key => $item) {
            $oriSubItems[$item['id']] = $item;
            unset($oriSubItems[$key]);
        }

        $table = get_instance()->db->dbprefix . $code;
        foreach ($items as $sequence => $item) {

            if (isset($item['changed']) && $item['changed'] == 'Y') {

                unset($item['changed']);

                // 存在於原本資料庫中的子項目
                if (isset($oriSubItems[$item['id']])) {
                    $created_at = date('Y-m-d H:i:s');
                    $item['sorted_at'] = $created_at;
                    $item['sequence'] = $sequence;
                    $item['updated_at'] = $created_at;
                    $this->database->where('id', $item['id']);
                    $this->database->update($table, $item);
                } else {

                    // 將子項目儲存至資料庫
                    $created_at = date('Y-m-d H:i:s');
                    $item['parent_id'] = $itemId;
                    $item['created_at'] = $created_at;
                    $item['updated_at'] = $created_at;
                    $item['sorted_at'] = $created_at;
                    $item['sequence'] = $sequence;
                    $this->database->insert($table, $item);
                }
            }

            if (isset($item['id']) && isset($oriSubItems[$item['id']])) {
                unset($oriSubItems[$item['id']]);
            }
        }

        // 沒被新增或更新的檔案，刪除
        foreach ($oriSubItems as $item) {
            $this->database->where('id', $item['id']);
            $this->database->delete($table);
        }
    }

    public function deleteSubItems($itemId, $code = '')
    {
        $table = get_instance()->db->dbprefix . $code;
        $subitems = $this->getSubItems($itemId, $code);
        foreach ($subitems as $item) {
            $this->database->where('id', $item['id']);
            $this->database->delete($table);
        }
    }
}
