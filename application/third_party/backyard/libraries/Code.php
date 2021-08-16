<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 後花園 - 代碼函式
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */

namespace backyard\libraries;

class Code
{
    public function getGUID()
    {
        if (function_exists('com_create_guid')) {
            return com_create_guid();
        } else {
            mt_srand((float)microtime() * 10000); //optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45); // "-"
            $uuid = '' //chr(123) // "{"
                . substr($charid, 0, 8) . $hyphen
                . substr($charid, 8, 4) . $hyphen
                . substr($charid, 12, 4) . $hyphen
                . substr($charid, 16, 4) . $hyphen
                . substr($charid, 20, 12);
                //. chr(125); // "}"
            return $uuid;
        }
    }
}
