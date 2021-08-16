<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . '/third_party/backyard/Backyard.php');
require_once(APPPATH . '/third_party/backyard/Package.php');


ini_set('display_errors', true);
error_reporting(E_ALL);

class Execute extends CI_Controller
{

    private $backyard = null;

    /**
     * 建構子
     */
    public function __construct()
    {
        parent::__construct();
        $this->backyard = new \backyard\Backyard();
        $this->backyard->setUser('admin');
    }

    public function sprint_tasks()
    {
        // 取得所有衝刺計畫中的項目
        $items = $this->backyard->data->getItems(array(
            'code' => 'item',
            'sprint' => 'Y'
        ));

        // 取得這個星期一、星期五的日期，做為項目的開始日期與結束日期
        $day = date('w');
        $week_start = date('Y-m-d', strtotime('-' . ($day - 1) . ' days'));
        $week_end = date('Y-m-d', strtotime('+' . (5 - $day) . ' days'));

        $tasks = array();
        foreach ($items['results'] as $item) {

            // 取得項目所屬的專案(為了取得他的標籤)
            $project = $this->backyard->data->getItem(array(
                'code' => 'project',
                'id' => $item['parent_id']
            ));

            // 取得項目底下的所有任務
            $itemTasks = $this->backyard->data->getItems(array(
                'code' => 'task',
                'parent_id' => $item['id']
            ));

            // 整理任務標題 => [專案標籤,項目(子)標籤]任務名稱
            foreach ($itemTasks['results'] as $task) {
                $task['source_title'] = $task['title'];
                $task['title'] = '[' . $project['item']['tag'] . ',' . $item['tag'] . ']' . $task['title'];
                $task['description'] = $item['reason'];
                $tasks[] = $task;
            }

            //$item['startdate'] = $week_start;
            //$item['deadline'] = $week_end;

            $response = $this->backyard->data->updateItem(array(
                'id'        => $item['id'],
                'startdate' => $week_start,
                'deadline'  => $week_end
            ), false, 'item');
        }

        /**
         * 工作時間只有星期一 ~ 星期五
         * 星期六、日要陪家人
         * 每天只能工作4個小時(按30分鐘為單位切割任務的話，就是8個單位)
         */

        $finalTasks = array();

        date_default_timezone_set('Asia/Taipei');

        // 距離星期五的剩餘天數
        $remainDays = 5 - date('w') + 1;

        // 平均每天要處理的數量
        $units_everyday = ceil(count($tasks) / $remainDays);

        for ($day = 0; $day < $remainDays; $day++) {
            $deadline = date('Y-m-d', strtotime('+' . $day . ' days'));

            $remainUnits = $units_everyday;
            foreach ($tasks as $key => $task) {
                $task['deadline'] = $deadline;
                $finalTasks[] = array(
                    'id' => $task['id'],
                    'title' => $task['title'],
                    'source_title' => $task['source_title'],
                    'description' => $task['description'],
                    'status' => $task['status'],
                    'google_task_id' => $task['google_task_id'],
                    'deadline'  => $task['deadline'],
                    'sequence'  => $task['sequence'],
                    'updated_at'  => $task['updated_at']
                );
                unset($tasks[$key]);
                if (--$remainUnits <= 0) break;
            }
        }
        $this->backyard->loadLibrary('WebForm');
        $webform = $this->backyard->webform;
        $webform->action('https://script.google.com/macros/s/AKfycbxiBTnwGt79UK2JpXnUF_C-mTZzbto8JE-jq9ki6l720oQXTRpRpWR8MuLwIYG_J1A/exec');
        $webform->method('POST');
        $webform->addField('tasks', json_encode($finalTasks));
        $jsonTasks = $webform->submit();
       
        $tasks = json_decode($jsonTasks, true);

        if (!is_null($tasks)) {
            foreach ($tasks as $task) {
                print_r($task);
                $this->backyard->data->updateItem(array(
                    'id'                => $task['id'],
                    'title'             => $task['source_title'],
                    'google_task_id'    => $task['google_task_id'],
                    'deadline'          => $task['deadline'],
                    'status'            => $task['status']
                ), false, 'task');
            }
        }
    }
}
