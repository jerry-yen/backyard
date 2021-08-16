<?php

/**
 * 後花園 - 後設資料處理
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */

namespace backyard\core;

class Metadata extends \backyard\core\Dao
{
    /**
     * 建構子
     */
    public function __construct($data = array())
    {
        $this->data = $data;
        $this->table = 'metadata';
    }

    /**
     * 取得多筆後設資料
     * 
     * @param string $metadataType 後設資料類型
     * @param string $toArray 轉換成陣列
     * 
     * @return Metadata[]
     */
    public function list($metadataType = '', $toArray = false)
    {
        if (get_instance()->backyard->getUserType() == 'master') {
            get_instance()->backyard->config->loadConfigFile('master');
            $master = get_instance()->backyard->config->getConfig('master');
            if (!isset($master[$metadataType])) {
                return array();
            }

            $metadatas = array();
            foreach ($master[$metadataType] as $key => $response) {
                if ($toArray) {
                    $metadatas[] = $response;
                } else {
                    $metadatas[] = new \backyard\core\Metadata($response);
                }
            }
        } else {
            $responses = parent::_list($this->table, array('metadata_type' => $metadataType));
            $metadatas = array();
            foreach ($responses as $response) {
                $metadata = json_decode($response['metadata'], true);
                $response = array_merge($response, $metadata);
                unset($response['metadata']);
                if ($toArray) {
                    $metadatas[] = $response;
                } else {
                    $metadatas[] = new \backyard\core\Metadata($response);
                }
            }
        }
        return $metadatas;
    }

    /**
     * 取得單筆後設資料
     * 
     * @param array $code 代碼
     * @param string $metadata_type 後設資料類型
     * @param string $toArray 轉換成陣列
     * 
     * @return Metadata
     */
    public function get($code, $metadataType = '', $toArray = false)
    {
        if (get_instance()->backyard->getUserType() == 'master') {
            get_instance()->backyard->config->loadConfigFile('master');
            $master = get_instance()->backyard->config->getConfig('master');
            if (!isset($master[$metadataType][$code])) {
                $metadata = new \backyard\core\Metadata(array());
            } else {
                $metadata = new \backyard\core\Metadata($master[$metadataType][$code]);
            }
        } else {
            $response = parent::_get($this->table, array('metadata_type' => $metadataType, 'code' => $code));
            if (is_null($response)) {
                $metadata = new \backyard\core\Metadata(array());
            } else {
                $metadata = json_decode($response['metadata'], true);
                $response = array_merge($response, $metadata);
                unset($response['metadata']);
                $metadata = new \backyard\core\Metadata($response);
            }
        }

        return ($toArray) ? $metadata->toArray() : $metadata;
    }

    /**
     * 根據識別碼取得單筆後設資料
     * 
     * @param string $metadataId 識別碼
     * 
     * @return Metadata
     */
    public function getById($metadataId, $toArray = false)
    {
        if (get_instance()->backyard->getUserType() == 'master') {
            exit('Backyard/core/Metadata::getById 開發者不可使用');
        } else {
            $response = parent::_get($this->table, array('id' => $metadataId));
            if (is_null($response)) {
                $metadata = new \backyard\core\Metadata(array());
            } else {
                $metadata = json_decode($response['metadata'], true);
                $metadata = is_null($metadata) ? array() : $metadata;
                $response = array_merge($response, $metadata);
                unset($response['metadata']);
                $metadata = new \backyard\core\Metadata($response);
            }

            return ($toArray) ? $metadata->toArray() : $metadata;
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
        // 取得 Dataset 驗證輸入的欄位值是否合法
        get_instance()->backyard->setUser('master');
        $dataset = get_instance()->backyard->dataset->get($metadataType);
        if($dataset->status == 'failed'){
            return $dataset;
        }

        $response = get_instance()->backyard->validator->checkInputs($dataset->toArray(), $data);
        if ($response['status'] == 'failed') {
            return $response;
        }
        
        // 將不在資料表的欄位資料，都以JSON型態
        // 集中在 metadata 欄位
        $metadata = array();
        $dbFields = parent::list_fields($this->table);

        foreach ($data as $field => $value) {
            if (!isset($dbFields[$field])) {
                $metadata[$field] = $value;
                unset($data[$field]);
            }
        }
        
        $data['metadata'] = json_encode($metadata, JSON_UNESCAPED_UNICODE);
        $data['metadata_type'] = $metadataType;
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $lastId = parent::_insert($this->table, $data);
        return $lastId;
    }

    /**
     * 更新後設資料
     * 
     * @param string $metadataType 後設資料類型
     * @param array $data 要更新的資料
     * @param boolean $escpeValidate 避開欄位驗證
     */
    public function update($metadataType, $data, $escpeValidate = false)
    {

        // 取得 Dataset 驗證輸入的欄位值是否合法
        get_instance()->backyard->setUser('master');
        $dataset = get_instance()->backyard->dataset->get($metadataType);

        if (!$escpeValidate) {
            $response = get_instance()->backyard->validator->checkInputs($dataset->toArray(), $data);
            if ($response['status'] == 'failed') {
                return $response;
            }
        }

        get_instance()->backyard->setUser('admin');
        $db = $this->getById($data['id'], true);
        $data = array_merge($db, $data);

        // 將不在資料表的欄位資料，都以JSON型態
        // 集中在 metadata 欄位
        $metadata = array();
        $dbFields = parent::list_fields($this->table);
        foreach ($data as $field => $value) {
            if (!isset($dbFields[$field])) {
                $metadata[$field] = $value;
                unset($data[$field]);
            }
        }

        $data['metadata'] = json_encode($metadata, JSON_UNESCAPED_UNICODE);

        $where = array(
            'id' => $data['id'],
            'metadata_type' => $metadataType
        );
        unset($data['id']);
        $data['updated_at'] = date('Y-m-d H:i:s');
        parent::_update($this->table, $data, $where);
    }

    /**
     * 刪除後設資料
     * 
     * @param string $metadataType 後設資料類型
     * @param string $id 後設資料識別碼
     **/
    public function delete($metadataType, $id)
    {
        $where = array(
            'id' => $id,
            'metadata_type' => $metadataType
        );
        parent::_delete($this->table, $where);
    }
}
