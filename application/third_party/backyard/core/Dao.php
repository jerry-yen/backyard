<?php

/**
 * 後花園 - Data Access Object 處理
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */

namespace backyard\core;

class Dao extends \backyard\Package
{
    protected $data = array();
    protected $table = '';

    /**
     * 魔術函式 - 動態變數值取得
     * 
     * @param string $name 變數名稱
     * 
     * @return Object
     */
    public function __get($name)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }
        return null;
    }

    /**
     * 魔術函式 - 動態變數值設定
     * 
     * @param string $name 變數名稱
     * @param object $value 變數值
     * 
     * @return Object
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * 取得多筆資料
     * @param string $table 資料表名稱
     * @param array $where 搜尋條件
     * @param array $sort 排序條件
     * @param array $fields 取得指定欄位
     */
    public function _list($table, $where = array(), $sort = array(), $fields = array())
    {

        // 顯示筆數，如果有值或該值 > 0，則代表要分頁
        $count_per_page = (isset($where['count'])) ? (int)$where['count'] : -1;
        unset($where['count']);

        // 目前頁數
        $page = (isset($where['page'])) ? (int)$where['page'] : 1;
        unset($where['page']);

        // 取得指定欄位
        if (is_array($fields) && count($fields) > 0) {
            get_instance()->backyard->database->select(implode(',', $fields));
        }

        // 搜尋條件
        if (is_array($where) && count($where) > 0) {
            get_instance()->backyard->database->where($where);
        }

        // 排序條件
        if (is_array($sort) && count($sort) > 0) {
            foreach ($sort as $field_name => $method) {
                get_instance()->backyard->database->order_by($field_name, $method);
            }
        }
        // 預設排序條件
        else {
            foreach (array('sorted_at' => 'ASC', 'sequence' => 'ASC', 'created_at' => 'DESC', 'updated_at' => 'DESC') as $field_name => $method) {
                get_instance()->backyard->database->order_by($field_name, $method);
            }
        }

        // 指定表單
        get_instance()->backyard->database =  get_instance()->backyard->database->from($table);

        // 分頁處理
        $totalPage = 1;
        $current_page = 1;
        if ($count_per_page > 0) {

            // 取得總筆數
            $total = get_instance()->backyard->database->count_all_results('', false);

            if ($count_per_page > $total) {
                $count_per_page = $total;
            }
            $totalPage = ceil($total / $count_per_page);
            $totalPage = ($totalPage == 0) ? 1 : $totalPage;
            
            $page = isset($page) ? $page : 1;
            $page = ($page < 1) ? 1 : $page;
            $page = ($page > $totalPage) ? $totalPage : $page;

            $offset = ($page - 1) * $count_per_page;
            get_instance()->backyard->database = get_instance()->backyard->database->limit($count_per_page, $offset);

            $current_page = (int)(isset($page) ? $page : 1);

            // 取得結果
            $results = get_instance()->backyard->database->get()->result_array();

            return array(
                'total' => $total,
                'total_page' => $totalPage,
                'current_page' => $current_page,
                'results' => $results
            );

        } else {

            // 取得結果
            $results = get_instance()->backyard->database->get()->result_array();

            return $results;
        }

        

       
        
    }

    /**
     * 取得單筆資料
     * @param string $table 資料表名稱
     * @param array $where 搜尋條件
     * @param array $sort 排序條件
     * @param array $fields 取得指定欄位
     */
    public function _get($table, $where = array(), $sort = array(), $fields = array())
    {
        // 取得指定欄位
        if (is_array($fields) && count($fields) > 0) {
            get_instance()->backyard->database->select(implode(',', $fields));
        }

        // 搜尋條件
        if (is_array($where) && count($where) > 0) {
            get_instance()->backyard->database->where($where);
        }

        // 排序條件
        if (is_array($sort) && count($sort) > 0) {
            foreach ($sort as $field_name => $method) {
                get_instance()->backyard->database->order_by($field_name, $method);
            }
        }
        // 預設排序條件
        else {
            foreach (array('created_at' => 'DESC', 'updated_at' => 'DESC') as $field_name => $method) {
                get_instance()->backyard->database->order_by($field_name, $method);
            }
        }

        $query = get_instance()->backyard->database->get($table);
        return $query->row_array();
    }

    /**
     * 新增資料
     * @param string $table 資料表名稱
     * @param array $data 欲新增的資料
     * 
     * @return string $insert_id 新增資料的識別碼
     */
    public function _insert($table, $data)
    {
        if (!isset($data['id'])) {
            get_instance()->backyard->loadLibrary('Code');
            $data['id'] = get_instance()->backyard->code->getGUID();
        }
        get_instance()->backyard->database->insert($table, $data);
        get_instance()->backyard->database->insert_id();
        return $data['id'];
    }

    /**
     * 修改資料
     * @param string $table 資料表名稱
     * @param array $data 欲修改的資料
     * @param array $where 搜尋條件
     */
    public function _update($table, $data, $where)
    {
        get_instance()->backyard->database->where($where);
        get_instance()->backyard->database->update($table, $data);
    }

    /**
     * 刪除資料
     * @param string $table 資料表名稱
     * @param array $where 搜尋條件
     */
    public function _delete($table, $where)
    {
        get_instance()->backyard->database->where($where);
        get_instance()->backyard->database->delete($table);
    }

    /**
     * 將物件轉換為陣列
     * 
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * 取得資料表中的欄位列表
     * 
     * @param string $table 資料表名稱
     * 
     * @return array 欄位列表
     */
    public function list_fields($table)
    {
        $fields = get_instance()->backyard->database->list_fields($table);
        foreach ($fields as $key => $field) {
            $fields[$field] = $field;
            unset($fields[$key]);
        }
        return $fields;
    }

    /**
     * 此物件是否存在
     * 
     * @return boolean
     */
    public function isExists()
    {
        return !($this->data == array());
    }
}
