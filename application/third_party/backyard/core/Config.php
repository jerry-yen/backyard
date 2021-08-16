<?php

/**
 * 後花園 - 設定管理
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */

namespace backyard\core;

class Config extends \backyard\Package
{
    /**
     * @var 設定目錄
     */
    private $configRootPath = APPPATH . 'third_party/backyard/config';

    /**
     * @var 設定值
     */
    private $config = array();

    /**
     * 載入設定檔
     * 
     * @param string $configFileName 設定檔名稱
     */
    public function loadConfigFile($configFileName)
    {
        require($this->configRootPath . '/' . $configFileName . '.php');
        if (isset($config)) {
            $this->config = $config;
        }
    }

    /**
     * 取得設定值
     * @param string $field 欄位名稱
     * 
     * @return $value
     */
    public function getConfig($field)
    {
        return $this->config[$field];
    }
}
