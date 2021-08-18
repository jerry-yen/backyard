<?php

/**
 * 後花園 - 關連處理
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */

namespace backyard\core;

class Relation extends \backyard\core\Item
{

    /**
     * 建構子
     */
    public function __construct($table = 'relation', $data = array())
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

    public function insert($table = '', $relation = array())
    {
        if ($table == '') {
            $table = $this->table;
        }

        if ($relation == array()) {
            $relation = $this->data;
        }

        $relation['created_at'] = (!isset($relation['created_at']) || $relation['created_at'] == '') ? date('Y-m-d H:i:s') : $relation['created_at'];
        $relation['updated_at'] = (!isset($relation['updated_at']) || $relation['updated_at'] == '') ? date('Y-m-d H:i:s') : $relation['updated_at'];

        $insert_id = parent::_insert($table, $relation);
        return $insert_id;
    }

    public function update($table = '', $relation = array())
    {
        if ($table == '') {
            $table = $this->table;
        }

        if ($relation == array()) {
            $relation = $this->data;
        }
        
        parent::_update($table, $relation, array('id' => $relation['id']));
    }
    
    public function delete($table, $data = array()){
        parent::_delete($table, array('id' => $data['id']));
    }
    
}
