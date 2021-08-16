<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 後花園 - 路徑函式
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */

namespace backyard\libraries;

class Path
{
    /**
     * 將絕對路徑改為網址的相對路徑
     */
    public function relative($absolute)
    {
        $rootPath = dirname(APPPATH);
        return str_replace($rootPath, '', $absolute);
    }
}
