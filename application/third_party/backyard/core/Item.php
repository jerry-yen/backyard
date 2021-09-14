<?php

/**
 * 後花園 - 資料項目
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */

namespace backyard\core;

class Item extends \backyard\core\Dao
{

    /**
     * 建構子
     */
    public function __construct($table = '', $data = array())
    {
        $this->data = $data;
        $this->table = $table;
    }



    /**
     * 取得多筆資料
     * @param string $table 資料表名稱
     * @param array $where 搜尋條件
     * @param array $sort 排序條件
     * @param array $fields 取得指定欄位
     */
    public function list($table, $where = array(), $sort = array(), $fields = array())
    {
        $responses = parent::_list($table, $where, $sort, $fields);

        if (!isset($responses['results'])) {
            $results = $responses;
            $responses = array(
                'total' => count($results),
                'total_page' => 1,
                'current_page' => 1
            );
        } else {
            $results = $responses['results'];
        }

        $dataset = get_instance()->backyard->dataset->get($table);

        $items = array();
        foreach ($results as $response) {
            $item = new \backyard\core\Item($table, $response);

            $fields = $dataset->fields;
            foreach ($fields as $field) {
                if (in_array($field['component'], array('imageupload', 'fileupload'))) {
                    $item->{$field['dbVariable']} = $item->getFiles($field['dbVariable'], true);
                } else if (in_array($field['component'], array('multiselect'))) {
                    $item->{$field['dbVariable']} = $item->getRelations($field['dbVariable'], true);
                }
            }
            $items[] = $item->toArray();
        }
        unset($responses['results']);
        $responses['results'] = $items;
        return $responses;
    }

    /**
     * 取得單筆資料
     * @param string $table 資料表名稱
     * @param array $where 搜尋條件
     * @param array $sort 排序條件
     * @param array $fields 取得指定欄位
     */
    public function get($table, $where = array(), $sort = array(), $fields = array())
    {
        $response = parent::_get($table, $where, $sort, $fields);
        $item = new \backyard\core\Item($table, $response);

        // 如果有特殊欄位(檔案、關連)
        $dataset = get_instance()->backyard->dataset->get($table);
        $fields = $dataset->fields;
        foreach ($fields as $field) {
            if (in_array($field['component'], array('imageupload', 'fileupload'))) {
                $item->{$field['dbVariable']} = $item->getFiles($field['dbVariable'], true);
            } else if (in_array($field['component'], array('multiselect'))) {
                $item->{$field['dbVariable']} = $item->getRelations($field['dbVariable'], true);
            }
        }

        return $item;
    }

    /**
     * 取得單筆資料
     * @param string $table 資料表名稱
     * @param array $where 搜尋條件
     * @param array $sort 排序條件
     * @param array $fields 取得指定欄位
     */
    public function empty($table)
    {
        $item = new \backyard\core\Item($table, array());
        return $item;
    }

    /**
     * 新增資料
     *  
     * @return string $insert_id 新增資料的識別碼
     */
    public function insert($table = '', $data = array())
    {
        if ($data == array()) {
            $data = $this->data;
        }

        $dataset = get_instance()->backyard->dataset->get($table);
        $response = get_instance()->backyard->validator->checkInputs($dataset->toArray(), $data);
        if ($response['status'] == 'failed') {
            return $response;
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        $insert_id = parent::_insert($table, $data);


        $emptyFields = array();

        // 逐個欄位確認是否有特殊欄位，例如：檔案上傳、子項目、關連項目
        $fields = $dataset->fields;
        foreach ($fields as $field) {

            // 檔案上傳
            if (in_array($field['component'], array('imageupload', 'fileupload'))) {

                $files = json_decode($data[$field['dbVariable']], true);
                foreach ($files as $key => $file) {
                    $file = new \backyard\core\File('file', $file);
                    $file->own_id = $insert_id;
                    $file->own_field = $field['dbVariable'];
                    $file->sorted_at = date('Y-m-d H:i:s');
                    $file->sequence = $key;
                    $file->moveTemporaryToUploadDirectory();
                    $file->insert();
                }

                $emptyFields[$field['dbVariable']] = '';
            }

            // 關連項目
            else if (in_array($field['component'], array('multiselect'))) {
                $relations = json_decode($data[$field['dbVariable']], true);
                foreach ($relations as $key => $relation) {
                    $item = new \backyard\core\Relation();
                    $item->source_id = $insert_id;
                    $item->source_field_variable = $field['dbVariable'];
                    $item->target_id = $relation;
                    $item->sorted_at = date('Y-m-d H:i:s');
                    $item->sequence = $key;
                    $item->insert();
                }

                $emptyFields[$field['dbVariable']] = '';
            }
        }

        if (count($emptyFields) > 0) {
            parent::_update($table, $emptyFields, array('id' => $insert_id));
        }

        return $insert_id;
    }

    /**
     * 修改資料
     */
    public function update($table = '', $data = array(), $skipValidation = false)
    {
        if ($data == array()) {
            $data = $this->data;
        }


        $dataset = get_instance()->backyard->dataset->get($table);
        if (!$skipValidation) {
            $response = get_instance()->backyard->validator->checkInputs($dataset->toArray(), $data);
            if ($response['status'] == 'failed') {
                return $response;
            }
        }


        $data['updated_at'] = date('Y-m-d H:i:s');
        parent::_update($table, $data, array('id' => $data['id']));

        $emptyFields = array();

        // 逐個欄位確認是否有特殊欄位，例如：檔案上傳、子項目、關連項目
        $fields = $dataset->fields;
        foreach ($fields as $field) {
            if (in_array($field['component'], array('imageupload', 'fileupload'))) {
                $this->data['id'] = $data['id'];

                // 取得原本資料庫裡的檔案清單
                $dbFiles = $this->getFiles($field['dbVariable'], true);
                foreach ($dbFiles as $key => $file) {
                    $dbFiles[$file['id']] = $file;
                    unset($dbFiles[$key]);
                }

                // 本次傳送來的檔案清單
                $reqFiles = json_decode($data[$field['dbVariable']], true);
                foreach ($reqFiles as $key => $file) {
                    $reqFiles[$file['id']] = $file;
                    unset($reqFiles[$key]);
                }

                // 對比檔案清單，資料庫有的，但本次送來的檔案清單沒有，代表資料庫要刪除該檔案
                foreach ($dbFiles as $id => $file) {
                    if (!isset($reqFiles[$id])) {
                        get_instance()->backyard->file->delete('file', $file);
                    }
                }

                // 比對檔案清單，本次送來的檔案清單有的檔案，資料庫的沒有，代表要新增
                $sequence = 0;
                foreach ($reqFiles as $id => $file) {
                    $file = new \backyard\core\File('file', $file);
                    if (!isset($dbFiles[$id])) {
                        $file->own_id = $data['id'];
                        $file->own_field = $field['dbVariable'];
                        $file->sorted_at = date('Y-m-d H:i:s');
                        $file->sequence = $sequence;
                        $file->moveTemporaryToUploadDirectory();
                        $file->insert();
                    } else {
                        $file->sorted_at = date('Y-m-d H:i:s');
                        $file->sequence = $sequence;
                        $file->update();
                    }

                    $sequence++;
                }

                $emptyFields[$field['dbVariable']] = '';
            }

            // 關連項目
            else if (in_array($field['component'], array('multiselect'))) {

                $this->data['id'] = $data['id'];

                // 取得原本資料庫裡的關連清單
                $dbRelations = $this->getRelations($field['dbVariable'], true);
                foreach ($dbRelations as $key => $relation) {
                    $dbRelations[$relation['target_id']] = $relation;
                    unset($dbRelations[$key]);
                }

                // 本次傳送來的關連清單
                $reqRelations = json_decode($data[$field['dbVariable']], true);
                foreach ($reqRelations as $key => $relation) {
                    $reqRelations[$relation] = $relation;
                    unset($reqRelations[$key]);
                }

                // 對比關連清單，資料庫有的，但本次送來的關連清單沒有，代表資料庫要刪除該關連
                foreach ($dbRelations as $id => $relation) {
                    if (!isset($reqRelations[$id])) {
                        get_instance()->backyard->item->delete('relation', $relation);
                    }
                }

                // 比對關連清單，本次送來的關連清單有的關連，資料庫的沒有，代表要新增
                $sequence = 0;
                foreach ($reqRelations as $id => $relation) {
                    $item = new \backyard\core\Relation();
                    if (!isset($dbRelations[$id])) {
                        $item->source_id = $data['id'];
                        $item->source_field_variable = $field['dbVariable'];
                        $item->target_id = $relation;
                        $item->sorted_at = date('Y-m-d H:i:s');
                        $item->sequence = $sequence;
                        $item->insert();
                    } else {
                        $item->id = $id;
                        $item->sorted_at = date('Y-m-d H:i:s');
                        $item->sequence = $sequence;
                        $item->update();
                    }

                    $sequence++;
                }

                $emptyFields[$field['dbVariable']] = '';
            }
        }

        if (count($emptyFields) > 0) {
            parent::_update($table, $emptyFields, array('id' => $data['id']));
        }
    }

    /**
     * 刪除資料
     */
    public function delete($table, $data = array())
    {
        if ($data == array()) {
            $data = $this->data;
        }

        parent::_delete($table, array('id' => $data['id']));

        // 如果底下有檔案及關連性資料，後續可以看要不要跟著刪掉
    }



    /**
     * 取得檔案
     * 
     * @param string $field_name 欄位名稱
     * 
     * @return array
     */
    public function getFiles($field_name, $toArray = false)
    {
        $responses = parent::_list('file', array('own_id' => $this->data['id'], 'own_field' => $field_name));
        if ($toArray) {
            return $responses;
        } else {
            $files = array();
            foreach ($responses as $file) {
                $files[] = new \backyard\core\File('file', $file);
            }
            return $files;
        }
    }

    /**
     * 取得關連的資料項目
     */
    public function getRelations($field_name, $toArray = false)
    {
        $responses = parent::_list('relation', array('source_id' => $this->data['id'], 'source_field_variable' => $field_name));
        if ($toArray) {
            return $responses;
        } else {
            $relations = array();
            foreach ($responses as $response) {
                $relations[] = new \backyard\core\Relation('relation', $response);
            }
            return $relations;
        }
    }

    /**
     * 取得關連的資料項目
     */
    public function getRelatiedItems($field_name, $toArray = false)
    {
    }

    /**
     * 取得子項目
     */
    public function getSubItems()
    {
    }
}
