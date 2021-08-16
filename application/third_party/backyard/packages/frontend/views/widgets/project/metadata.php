<?php
$metadata = array(
    'fields'   => array(
        array(
            'name' => '分類層數', 
            'dbVariable' => 'classLevel', 
            'frontendVariable' => 'classLevel', 
            'component' => 'number', 
            'validator' => array(), 
            'converter' => array(), 
            'source' => '', 
            'fieldTip' => ''
        ),
        array(
            'name' => '操作權限', 
            'dbVariable' => 'permission', 
            'frontendVariable' => 'permission', 
            'component' => 'checkbox', 
            'validator' => array(), 
            'converter' => array(), 
            'source' => '{"ADD":"新增","MODIFY":"修改","DELETE":"刪除","SORT":"排序","EXPORT":"匯出","IMPORT":"匯入"}',
            'fieldTip' => ''
        ),
        array(
            'name' => '清單欄位顯示', 
            'dbVariable' => 'listfields', 
            'frontendVariable' => 'listfields', 
            'component' => 'listfields', 
            'validator' => array(), 
            'converter' => array(), 
            'source' => '', 
            'fieldTip' => ''
        ),
        array(
            'name' => '子項目', 
            'dbVariable' => 'sublist', 
            'frontendVariable' => 'sublist', 
            'component' => 'sublist', 
            'validator' => array(), 
            'converter' => array(), 
            'source' => '', 
            'fieldTip' => ''
        ),
        array(
            'name' => '子項目層數', 
            'dbVariable' => 'sublist_level', 
            'frontendVariable' => 'sublist_level', 
            'component' => 'text', 
            'validator' => array(), 
            'converter' => array(), 
            'source' => '', 
            'fieldTip' => '-1:最後一層,可使用「,」來指定多個層數，例如：1,3，代表1、3層要出現子項目清單鈕'
        ),
    ),
    'events' => array(
        array(
            'name' => '新增事件API', 
            'dbVariable' => 'add', 
            'frontendVariable' => 'add', 
            'component' => 'text', 
            'validator' => array(), 
            'converter' => array(), 
            'source' => '', 
            'fieldTip' => ''
        ),

        array(
            'name' => '修改事件API', 
            'dbVariable' => 'modify', 
            'frontendVariable' => 'modify', 
            'component' => 'text', 
            'validator' => array(), 
            'converter' => array(), 
            'source' => '', 
            'fieldTip' => ''
        ),

        array(
            'name' => '刪除事件API', 
            'dbVariable' => 'delete', 
            'frontendVariable' => 'delete', 
            'component' => 'text', 
            'validator' => array(), 
            'converter' => array(), 
            'source' => '', 
            'fieldTip' => ''
        ),

        array(
            'name' => '資料來源API', 
            'dbVariable' => 'dataSource', 
            'frontendVariable' => 'dataSource', 
            'component' => 'text', 
            'validator' => array(), 
            'converter' => array(), 
            'source' => '', 
            'fieldTip' => ''
        )
    ),
);
