<?php

/**
 * 後花園 - 開發者資料處理
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */

namespace backyard\datahandler;

class Master extends \backyard\Package
{
    /**
     * 取得資料集後設資料
     * 
     * @param strin $code 代碼
     */
    public function getDataset($code)
    {
        $this->backyard->config->loadConfigFile('master');
        $master = $this->backyard->config->getConfig('master');
        if (!isset($master['dataset'][$code])) {
            return array('status' => 'failed', 'code' => 'dataset', 'message' => '找不到Master設定');
        } else {
            return array('status' => 'success', 'code' => 'dataset', 'dataset' => $master['dataset'][$code]);
        }
    }

    /**
     * 取得組件後設資料
     * 
     * @param strin $code 代碼
     */
    public function getMetadataOfWidget($code)
    {
        $this->backyard->config->loadConfigFile('master');
        $master = $this->backyard->config->getConfig('master');

        if (!isset($master['widget'][$code])) {
            return array('status' => 'failed', 'message' => '找不到此組件');
        }
        return array('status' => 'success', 'metadata' => $master['widget'][$code]);
    }

    /**
     * 取得頁面後設資料
     * 
     * @param strin $code 代碼
     */
    public function getMetadataOfPage($code)
    {
        $this->backyard->config->loadConfigFile('master');
        $master = $this->backyard->config->getConfig('master');
        if (!isset($master['page'][$code])) {
            return array('status' => 'failed', 'message' => '找不到此頁面');
        }

        return array('status' => 'success', 'metadata' => $master['page'][$code]);
    }

    /**
     * 取得頁面後設資料
     * 
     * @param strin $code 代碼
     */
    public function getMetadataOfPages()
    {
        $response = $this->backyard->data->getItems(array('code' => 'content'));
        return array('status' => 'success', 'metadata' => $response['results']);
    }

    public function getSystemInformation()
    {
        $this->backyard->config->loadConfigFile('master');
        $master = $this->backyard->config->getConfig('master');
        return array('status' => 'success', 'metadata' => $master['login']);
    }

    /**
     * 取得版面後設資料
     * 
     * @param strin $code 代碼
     */
    public function getMetadataOfTemplate($code)
    {
        $this->backyard->config->loadConfigFile('master');
        $master = $this->backyard->config->getConfig('master');
        return array('status' => 'success', 'metadata' => $master['template'][$code]);
    }

    /**
     * 將資料庫資料轉換成一般欄位的資料
     * 
     * @param array $result 資料庫資料
     * @return array 一般欄位
     */
    public function convertToData($result)
    {
        if (isset($result['metadata'])) {
            $data = json_decode($result['metadata'], true);
            $result = array_merge($result, $data);
            unset($result['metadata']);
        }

        if (isset($result['code'])) {
            $result['_code'] = $result['code'];
            unset($result['code']);
        }

        return $result;
    }

    /**
     * 將一般欄位的資料轉換成資料庫資料
     * 
     * @param array $value 資料庫的值
     * 
     * @return array 資料庫資料
     */
    public function convertToDatabase($value)
    {
        $module['id'] = $value['id'];

        if (isset($value['created_at'])) {
            $module['created_at'] = $value['created_at'];
        }
        if (isset($value['updated_at'])) {
            $module['updated_at'] = $value['updated_at'];
        }
        if (isset($value['_code'])) {
            $module['code'] = $value['_code'];
        }
        if (isset($value['code'])) {
            $module['config_type'] = $value['code'];
        }
        $table = get_instance()->db->dbprefix . 'module';

        unset($value['id']);
        unset($value['created_at']);
        unset($value['updated_at']);
        unset($value['code']);
        unset($value['_code']);
        $module['metadata'] = json_encode($value, JSON_UNESCAPED_UNICODE);
        unset($value);

        // 整理好的值，重新付予給value變數
        $value = $module;
        unset($module);
        return array('table' => $table, 'value' => $value);
    }

    /**
     * 將一般欄位的資料轉換成資料庫條件
     * 
     * @param array $value 條件值
     * 
     * @return array 資料庫資料
     */
    public function convertToWhere($value)
    {
        $module = array();
        if (isset($value['id'])) {
            $module['id'] = $value['id'];
        }
        if (isset($value['created_at'])) {
            $module['created_at'] = $value['created_at'];
        }
        if (isset($value['updated_at'])) {
            $module['updated_at'] = $value['updated_at'];
        }
        if (isset($value['code'])) {
            $module['config_type'] = $value['code'];
        }
        $table = get_instance()->db->dbprefix . 'module';
        return array('table' => $table, 'where' => $module);
    }

    /**
     * 開發者登入
     * @param array $data
     */
    public function login($data)
    {
        $metadata = $this->getSystemInformation();
        if ($metadata['status'] != 'success') {
            return array('status' => 'failed', 'message' => '設定檔錯誤');
        }

        if (
            $data['account'] == $metadata['metadata']['account']
            && $data['password'] == $metadata['metadata']['password']
        ) {
            get_instance()->session->set_userdata('backyard_master_login', 'developer');
            return array('status' => 'success', 'message' => '登入成功', 'landing_page' => 'page/login');
        } else {
            get_instance()->session->unset_userdata('backyard_master_login');
            return array('status' => 'failed', 'message' => '帳號或密碼錯誤');
        }
    }
}
