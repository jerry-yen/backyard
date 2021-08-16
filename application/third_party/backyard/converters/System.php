<?php

/**
 * 後花園 - 系統轉換器
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */

namespace backyard\converters;

class System
{
    /**
     * 轉換為對應陣列的值
     * 
     * @param $value 值
     * @param array $datas 條列資料
     * 
     * @return array
     */
    public function selectOne($value, $datas = array())
    {
        foreach ($datas as $key => $data) {
            $parts = explode(':', $data);
            $datas[$parts[0]] = $parts[1];
            unset($datas[$key]);
        }

        if (isset($datas[$value])) {
            return array('status' => 'success', 'value' => $datas[$value]);
        }

        return array('status' => 'failed', 'message' => $value . ' => 找不到對應值');
    }
}
