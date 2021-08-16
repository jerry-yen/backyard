<?php

/**
 * 後花園 - 套件管理
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */

namespace backyard\core;

class Package
{

    /**
     * @var 套件
     */
    private $packages = array();

    /**
     * 魔術函式
     * 
     * @param string $method 函式名稱
     * @param array $arguments 參數
     */
    public function __call($method, $arguments)
    {
        foreach ($this->packages as $package) {
            if (method_exists($package, $method)) {
                $package->$method($arguments);
            }
        }
    }

    /**
     * 載入套件
     * 
     * @param string $packageName 套件名稱
     */
    public function loadPackage($packageName)
    {
        if (!isset($this->packages[$packageName])) {
            $this->packages[$packageName] = new $packageName();
        }
    }
}
