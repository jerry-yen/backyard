<?php

/**
 * 後花園 - 系統驗證器
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */

namespace backyard\validators;

class System
{
    /**
     * 是否必填
     * 
     * @param $field 欄位名稱
     * @param $value 值
     * @param array $params 額外參數
     * 
     * @return array status(success:驗證通過,failed:驗證失敗), message:錯誤訊息
     */
    public function required($field, $value, $params = array())
    {
        $res = !($value == '' || is_null($value));

        if (!$res) {
            return array('status' => 'failed', 'message' => $field . '為必填欄位');
        } else {
            return array('status' => 'success');
        }
    }

    /**
     * 字串長度是否在範圍內
     * 
     * @param $field 欄位名稱
     * @param $value 值
     * @param array $params 額外參數 (integer $min 最小值, integer $max 最大值, $charset 字元編碼(預設UTF-8,optional)
     * 
     * @return array status(success:驗證通過,failed:驗證失敗), message:錯誤訊息
     */
    public function length($field, $value, $params = array())
    {
        // 不填寫則不驗證
        if ($value == '' || is_null($value)) {
            return array('status' => 'success');
        }

        $min = $params[0];
        $max = $params[1];
        $charset = isset($params[2]) ? $params[2] : 'utf-8';

        $length = mb_strlen($value, $charset);
        $res = ($min <= $length && $length <= $max);

        if (!$res) {
            return array('status' => 'failed', 'message' => $field . '必需介於' . $min . '~' . $max . '個字');
        } else {
            return array('status' => 'success');
        }
    }

    /**
     * 是否在範圍內
     * 
     * @param $field 欄位名稱
     * @param $value 值
     * @param array $params 額外參數 (integer $min 最小值, integer $max 最大值)
     * 
     * @return array status(success:驗證通過,failed:驗證失敗), message:錯誤訊息
     */
    public function range($field, $value, $params = array())
    {
        // 不填寫則不驗證
        if ($value == '' || is_null($value)) {
            return array('status' => 'success');
        }
        $min = $params[0];
        $max = $params[1];
        $res = ($min <= $value && $value <= $max);

        if (!$res) {
            return array('status' => 'failed', 'message' => $field . '必需介於' . $min . '~' . $max . '之間');
        } else {
            return array('status' => 'success');
        }
    }

    /**
     * 列舉
     * 
     * @param $field 欄位名稱
     * @param $value 值
     * @param array $params 額外參數 (列舉的值)
     * 
     * @return array status(success:驗證通過,failed:驗證失敗), message:錯誤訊息
     */
    public function enum($field, $value, $params)
    {
        // 不填寫則不驗證
        if ($value == '' || is_null($value)) {
            return array('status' => 'success');
        }
        foreach ($params as $param) {
            if ($value == $param) {
                return array('status' => 'success');
            }
        }
        return array('status' => 'failed', 'message' => $field . '的值必需是' . implode(',', $params));
    }

    /**
     * UUID格式
     * 
     * @param $field 欄位名稱
     * @param string $value 值
     * @param array $params 額外參數
     * 
     * @return array status(success:驗證通過,failed:驗證失敗), message:錯誤訊息
     */
    public function uuid($field, $value, $params)
    {
        // 不填寫則不驗證
        if ($value == '' || is_null($value)) {
            return array('status' => 'success');
        }
        $res = preg_match("/^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$/", $value);
        if (!$res) {
            return array('status' => 'failed', 'message' => $field . '的UUID格式錯誤');
        } else {
            return array('status' => 'success');
        }
    }
    /**
     * 信箱格式
     * 
     * @param $field 欄位名稱
     * @param string $value 值
     * @param array $params 額外參數
     * 
     * @return array status(success:驗證通過,failed:驗證失敗), message:錯誤訊息
     */
    public function email($field, $value, $params = array())
    {
        // 不填寫則不驗證
        if ($value == '' || is_null($value)) {
            return array('status' => 'success');
        }
        $res = preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i", $value);

        if (!$res) {
            return array('status' => 'failed', 'message' => $field . '的信箱格式錯誤');
        } else {
            return array('status' => 'success');
        }
    }

    /**
     * 日期時間格式
     * 
     * @param $field 欄位名稱
     * @param string $value 值
     * @param array $params 額外參數
     * 
     * @return array status(success:驗證通過,failed:驗證失敗), message:錯誤訊息
     */
    public function datetime($field, $value, $params = array())
    {
        // 不填寫則不驗證
        if ($value == '' || is_null($value)) {
            return array('status' => 'success');
        }
        $res = preg_match("/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/i", $value);

        if (!$res) {
            return array('status' => 'failed', 'message' => $field . '的日期時間格式錯誤');
        } else {
            return array('status' => 'success');
        }
    }
}
