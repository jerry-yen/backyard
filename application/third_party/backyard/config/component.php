<?php

/*************************************
 *             元件
 *************************************/

$config['components'] = array(
    array(
        'group' => 'HTML元件', 'components' => array(
            array('name' => '文字方塊', 'component' => 'text'),
            array('name' => '多行文字', 'component' => 'textarea'),
            array('name' => '數字', 'component' => 'number'),
            array('name' => '單選下拉', 'component' => 'select'),
        )
    ),

    array(
        'group' => '區塊元件', 'components' => array(
            array('name' => '群組標籤', 'component' => 'grouplabel')
        )
    ),

    array(
        'group' => 'jQuery套件', 'components' => array(
            array('name' => '開關閘', 'component' => 'switch'),
            array('name' => '多選下拉', 'component' => 'multiselect'),
        )
    ),
  

);
