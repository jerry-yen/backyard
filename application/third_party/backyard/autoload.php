<?php

/**
 * 後花園 - 自動載入套件
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */
function autoload($className) {
    $classPath = str_replace('\\','/',$className);
    $filename = dirname(__DIR__) . "/" . $classPath . ".php";
    if (is_readable($filename)) {
        require $filename;
    }
}

spl_autoload_register('autoload');

require_once(dirname(__FILE__) . '/Backyard.php');
require_once(dirname(__FILE__) . '/Package.php');