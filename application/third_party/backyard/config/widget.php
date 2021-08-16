<?php

/*************************************
 *               組件
 *************************************/

$config['widgets'] = array(
    array(
        'group' => '資料', 'widgets' => array(
            array('name' => '資料清單', 'widget' => 'data'),
            array('name' => '資料表單', 'widget' => 'form'),
            array('name' => '便利貼', 'widget' => 'sticky'),
            array('name' => '專案管理', 'widget' => 'project'),
        )
    ),

    array(
        'group' => '版面', 'widgets' => array(
            array('name' => '頁頭', 'widget' => 'header'),
            array('name' => '頁尾', 'widget' => 'footer'),
            array('name' => '選單', 'widget' => 'menu'),
            array('name' => '麵包屑', 'widget' => 'breadcrumb'),
            array('name' => '登入表單', 'widget' => 'login'),
        )
    ),

    array(
        'group' => '訊息', 'widgets' => array(
            array('name' => '通知', 'widget' => 'notification'),
        )
    ),
  

);
