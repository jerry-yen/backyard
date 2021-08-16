<?php

/**
 * 後花園 - 資料集後設資料處理
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */

namespace backyard\core;

class Dataset extends \backyard\core\Metadata
{

    /**
     * 取得所有資料集
     * 
     * @param string $metadata_type 後設資料類型
     * @param string $toArray 轉換成陣列
     * 
     * @return Dataset[]
     */
    public function list($metadataType = 'dataset', $toArray = false)
    {
        $datasets = array();
        $responses = parent::list($metadataType, $toArray);
        if ($toArray) {
            $datasets = $responses;
        } else {
            foreach ($responses as $response) {
                $datasets[] = new \backyard\core\Dataset($response->toArray());
            }
        }

        return $datasets;
    }


    /**
     * 取得單筆資料集
     * 
     * @param array $code 代碼
     * @param string $metadata_type 後設資料類型
     * @param string $toArray 轉換成陣列
     * 
     * @return Dataset
     */
    public function get($code, $metadataType = 'dataset', $toArray = false)
    {
        if (isset($code) && trim($code) != '') {
            $response = parent::get($code, $metadataType, $toArray);
            if ($toArray) {
                $response['fields'] = (is_string($response['fields'])) ? json_decode($response['fields'], true) : $response['fields'];
                return $response;
            } else {
                $response->fields = (is_string($response->fields)) ? json_decode($response->fields, true) : $response->fields;
                return new \backyard\core\Dataset($response->toArray());
            }
        } else {
            return new \backyard\core\Dataset(array());
        }
    }

    /**
     * 新增後設資料
     * 
     * @param string $metadataType 後設資料類型
     * @param array $data 要新增的資料
     * 
     * @return string 最後新增的識別碼
     */
    public function insert($metadataType, $data)
    {
        $last_id = parent::insert($metadataType, $data);

        // 依據Dataset的欄位設定，動態新增資料表
        $this->buildTable($data);
        return $last_id;
    }

    /**
     * 更新資料集
     * 
     * @param string $metadataType 後設資料類型
     * @param array $data 要更新的資料
     * @param boolean $escpeValidate 避開欄位驗證
     */
    public function update($metadataType = 'dataset', $data, $escpeValidate = false)
    {
        parent::update($metadataType, $data, $escpeValidate);

        // 依據Dataset的欄位設定，動態更新資料表
        $this->buildTable($data);
    }

    /**
     * 刪除資料集
     * 
     * @param string $metadataType 後設資料類型
     * @param string $id 後設資料識別碼
     **/
    public function delete($metadataType = 'dataset', $id)
    {
        $response = parent::getById($id, true);
        
        $code = $response['code'];

        // 表單名稱
        $tableName =  get_instance()->backyard->database->dbprefix . $code;
        
        // 刪除資料表
        $sql = 'DROP TABLE IF EXISTS ' . $tableName;
        get_instance()->backyard->database->query($sql);

        parent::delete($metadataType, $id);
    }

    /**
     * 建置
     */
    private function buildTable($dataset)
    {
        $code = $dataset['code'];
        $fields = json_decode($dataset['fields'], true);

        // 表單名稱
        $tableName =  get_instance()->backyard->database->dbprefix . $code;

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

        get_instance()->backyard->database->query($sql);

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
        $dbFields = get_instance()->backyard->database->list_fields($tableName);
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

            get_instance()->backyard->database->query($sql);

            $fields[$field['dbVariable']] = $field;
        }

        foreach ($dbFields as $field => $value) {
            if (!isset($fields[$field]) && !isset($defualtFields[$field])) {
                $sql = 'ALTER TABLE ' . $tableName . ' DROP COLUMN ' . $field . ';';
                get_instance()->backyard->database->query($sql);
            }
        }
    }

    public function getComponents()
    {
        if (!isset($this->data['fields'])) {
            return array();
        }

        if (is_string($this->data['fields']) && trim($this->data['fields']) != '') {
            $this->data['fields'] = json_decode($this->data['fields'], true);
        }

        $components = array();
        
        foreach ($this->data['fields'] as $field) {
            $components[] = new \backyard\packages\frontend\Component($field);
        }

        return $components;
    }
}
