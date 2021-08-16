<?php

/**
 * 後花園系統主程式
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */

namespace backyard;

class Backyard
{
    /**
     * var Object 使用者
     */
    private $user = null;

    /**
     * @var array POST + GET 輸入值
     */
    private $inputs = array();

    /**
     * @var 套件
     */
    private $packages = array();

    /**
     * @var 函式
     */
    private $libraries = array();

    /**
     * @var 使用者類型(admin|master)
     */
    private $userType = 'admin';

    /**
     * @var 資料庫連線
     */
    public $database;

    public function database_connection($connectionName = 'default')
    {
        // 從設定檔取得資料庫連線
        $this->packages['core']['Config']->loadConfigFile('database');
        $connectionConfigs =  $this->packages['core']['Config']->getConfig('database');
        $connectionConfig = isset($connectionConfigs[$connectionName]) ?
            $connectionConfigs[$connectionName] :
            $connectionConfigs['default'];

        get_instance()->load->database($connectionConfig);
        $this->database = get_instance()->db;
    }

    /**
     * 建構子
     */
    public function __construct()
    {
        date_default_timezone_set('Asia/Taipei');

        // 載入核心套件
        $this->loadCorePackage();

        // 資料庫連線
        $this->database_connection();

        // 過濾IP
        $this->filterIPs();

        // 取得所有輸入變數(POST + GET)
        $this->mergeInputs();
    }

    /**
     * 魔術函式 - 動態函式呼叫
     * 
     * @param string $method 函式名稱
     * @param array $arguments 參數
     */
    public function __call($method, $arguments)
    {
        foreach ($this->packages as $classes) {
            foreach ($classes as $classObject) {
                if (method_exists($classObject, $method)) {
                    $classObject->$method($arguments);
                    return;
                }
            }
        }
    }

    /**
     * 魔術函式 - 動態變數值取得
     * 
     * @param string $name 變數名稱
     * 
     * @return Object
     */
    public function __get($name)
    {
        foreach ($this->packages as $classes) {    
            foreach ($classes as $className => $classObject) {
                if (strtolower($name) == strtolower($className)) {
                    return $classObject;
                }
            }
        }

        foreach ($this->libraries as $className => $classObject) {
            if (strtolower($name) == strtolower($className)) {
                return $classObject;
            }
        }

        return null;
    }

    /**
     * 載入套件
     * 
     * @param string $packageName 套件名稱
     * @param string $namespace 命名空間
     */
    private function loadingPackage($packageName, $namespace, $packagePath)
    {
        if (!isset($this->packages[$packageName])) {
            $this->packages[$packageName] = array();
        }

        $packagePath = $packagePath . '/' . $packageName;
        $classFiles = scandir($packagePath);
        foreach ($classFiles as $file) {
            // 不處理目錄
            if (
                in_array($file, array('.', '..')) ||
                is_dir($packagePath . '/' . $file)
            ) {
                continue;
            }

            $ext = substr(strrchr($file, '.'), 1);
            if (in_array($ext, array('php'))) {
                // 載入套件檔案
                require_once($packagePath . '/' . $file);
                $dot = strripos($file, '.');
                $className = substr($file, 0, ($dot !== false) ? $dot : strlen($file));
                if (!isset($this->packages[$packageName][$className])) {
                    $classPath = $namespace . '\\' . $className;
                    $this->packages[$packageName][$className] = new $classPath($this);
                }
            }
        }
    }

    /**
     * 載入核心套件
     * 
     * @param string $packageName 套件名稱
     */
    private function loadCorePackage()
    {
        $packageName = 'core';
        $namespace = '\\backyard\\' . $packageName;
        $packagePath = dirname(__FILE__);
        $this->loadingPackage($packageName, $namespace, $packagePath);
    }

    /**
     * 載入擴充套件
     * 
     * @param string $packageName 套件名稱
     */
    public function loadPackage($packageName)
    {
        $namespace = '\\backyard\\packages\\' . $packageName;
        $packagePath = dirname(__FILE__) . '/packages';
        $this->loadingPackage($packageName, $namespace, $packagePath);
    }

    public function loadLibrary($libraryName)
    {
        $namespace = '\\backyard\\libraries';
        $libraryPath = dirname(__FILE__) . '/libraries';

        // 載入套件檔案
        require_once($libraryPath . '/' . $libraryName . '.php');

        if (!isset($this->libraries[$libraryName])) {
            $classPath = $namespace . '\\' . $libraryName;
            $this->libraries[$libraryName] = new $classPath();
        }
    }

    /**
     * 設定使用者
     * 
     * @param Object $user (master:開發者, admin:管理者)
     */
    public function setUser($userType = 'admin')
    {
        $this->userType = $userType;

        $namespace = '\\backyard\\datahandler';
        $className = ucfirst($userType);
        require_once(dirname(__FILE__) . '/datahandler/' . $className . '.php');

        $className = $namespace . '\\' . $className;
        $this->user = new $className($this);
    }

    /**
     * 取得使用者類型
     * 
     * @return Object
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * 取得使用者類型
     */
    public function getUserType()
    {
        return $this->userType;
    }

    /**
     * 取得GET、POST資料
     */
    private function mergeInputs()
    {
        // 專用於 Restful API時，取得 GET 所使用
        if (method_exists(\CI_Controller::get_instance(), 'get')) {
            $this->inputs = array_merge(
                \CI_Controller::get_instance()->get(),
                get_instance()->input->get()
            );
            $this->inputs = array_merge(
                $this->inputs,
                get_instance()->input->post()
            );
            $this->inputs = array_merge(
                $this->inputs,
                \CI_Controller::get_instance()->put()
            );
            $this->inputs = array_merge(
                $this->inputs,
                \CI_Controller::get_instance()->delete()
            );
        } else {
            $this->inputs = array_merge(
                get_instance()->input->get(),
                get_instance()->input->post()
            );
        }
    }

    /**
     * 過濾IP
     */
    private function filterIPs()
    {
        $response = $this->security->filterIPs();
        if ($response['status'] == 'deny') {

            // [待處理]之後不能直接Exit，要轉向其他畫面
            exit('Deny Your IP');
        }
        unset($security);
    }

    public function getInputs($exValues = array())
    {
        return array_merge($this->inputs, $exValues);
    }
}
