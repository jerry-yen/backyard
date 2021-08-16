<?php

/**
 * 後花園 - 專案轉換器
 * 
 * @author Jerry Yen - yenchiawei@gmail.com
 */

namespace backyard\converters;

class Project
{
    /**
     * 轉換為對應陣列的值
     * 
     * @param $value 值
     * @param array $params 參數資料
     * 
     * @return array
     */
    public function project_progress($value, $params = array(), $item = array())
    {
        // 取得專案底下的所有任務      
        $tasks = get_instance()->db->query('SELECT * FROM byard_item, byard_task WHERE byard_item.parent_id="' . $item['id'] . '" AND byard_item.id=byard_task.parent_id')->result_array();

        $completed = 0;
        foreach($tasks as $task){
            if($task['status'] == 2){
                $completed++;
            }
        }
        $total = count($tasks);
        $percent = ($total==0)?0:((int)($completed/$total*100));
        return array('status' => 'success', 'value' => $completed . ' / ' . $total . ' (' . $percent . '%)');
    }

    /**
     * 轉換為對應陣列的值
     * 
     * @param $value 值
     * @param array $params 參數資料
     * 
     * @return array
     */
    public function item_progress($value, $params = array(), $item = array())
    {
        // 取得專案底下的所有任務      
        $tasks = get_instance()->db->query('SELECT * FROM byard_task WHERE byard_task.parent_id="' . $item['id'] . '"')->result_array();

        $completed = 0;
        foreach($tasks as $task){
            if($task['status'] == 2){
                $completed++;
            }
        }
        $total = count($tasks);
        $percent = ($total==0)?0:((int)($completed/$total*100));
        return array('status' => 'success', 'value' => $completed . ' / ' . $total . ' (' . $percent . '%)');
    }
}
