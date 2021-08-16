<?php

/*********************************************************************************
 *                                    後花園 v2
 *********************************************************************************/


/*************************************
 *             開發者帳密
 *************************************/

$config['master']['information']['information'] = array(
    'title'     => '開發者管理平台',
    'account'   => 'develop',
    'password'  => '520726428',
    'theme'     => 'login/tech'
);


/*************************************
 *             資料集
 *************************************/

/**
 * 信箱資料集
 */
$config['master']['dataset']['email'] = array(
    'name'              => '信箱資料集',
    'metadata_type'     => 'email',
    'fields'        => array(
        array('name' => '代碼', 'dbVariable' => 'code', 'frontendVariable' => 'code', 'component' => 'text', 'validator' => array('required', 'length{3,20}'), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '用述', 'dbVariable' => 'title', 'frontendVariable' => 'title', 'component' => 'text', 'validator' => array('required', 'length{5,30}'), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '信箱設定', 'dbVariable' => 'emailSetting', 'frontendVariable' => 'emailSetting', 'component' => 'grouplabel', 'validator' => array(), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '發送信箱', 'dbVariable' => 'email', 'frontendVariable' => 'email', 'component' => 'text', 'validator' => array('required', 'email'), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '發送名稱', 'dbVariable' => 'emailName', 'frontendVariable' => 'emailName', 'component' => 'text', 'validator' => array('length{5,20}'), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '回覆信箱', 'dbVariable' => 'replyEmail', 'frontendVariable' => 'replyEmail', 'component' => 'text', 'validator' => array('required', 'email'), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => 'SMTP開關', 'dbVariable' => 'smtpGate', 'frontendVariable' => 'smtpGate', 'component' => 'switch', 'validator' => array('required', 'enum{Y,N}'), 'converter' => array(), 'source' => '["是","否"]', 'fieldTip' => ''),
        array('name' => 'SMTP設定', 'dbVariable' => 'smtpSetting', 'frontendVariable' => 'smtpSetting', 'component' => 'grouplabel', 'validator' => array(), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '主機', 'dbVariable' => 'smtpHost', 'frontendVariable' => 'smtpHost', 'component' => 'text', 'validator' => array(), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => 'Port', 'dbVariable' => 'smtpPort', 'frontendVariable' => 'smtpPort', 'component' => 'number', 'validator' => array(), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '帳號', 'dbVariable' => 'smtpAccount', 'frontendVariable' => 'smtpAccount', 'component' => 'text', 'validator' => array('length{5,50}'), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '是否需要驗證', 'dbVariable' => 'isVerification', 'frontendVariable' => 'isVerification', 'component' => 'switch', 'validator' => array('enum{Y,N}'), 'converter' => array(), 'source' => '["是","否"]', 'fieldTip' => ''),
        array('name' => '安全協定', 'dbVariable' => 'smtpSecure', 'frontendVariable' => 'smtpSecure', 'component' => 'select', 'validator' => array('required', 'enum{,SSL,TLS}'), 'converter' => array(), 'source' => '{"":"無","SSL":"SSL","TLS":"TLS"}', 'fieldTip' => ''),
    )
);


/**
 * 用戶資料集
 */
$config['master']['dataset']['user'] = array(
    'name'              => '用戶資料集',
    'metadata_type'     => 'user',
    'fields'        => array(
        array('name' => '代碼', 'dbVariable' => 'code', 'frontendVariable' => 'code', 'component' => 'text', 'validator' => array('required', 'length{3,20}'), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '名稱', 'dbVariable' => 'name', 'frontendVariable' => 'name', 'component' => 'text', 'validator' => array('required', 'length{3,10}'), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '是否可登入', 'dbVariable' => 'isLogin', 'frontendVariable' => 'isLogin', 'component' => 'switch', 'validator' => array('required', 'enum{Y,N}'), 'converter' => array(), 'source' => '["是","否"]', 'fieldTip' => ''),
        array('name' => '所屬群組', 'dbVariable' => 'group', 'frontendVariable' => 'group', 'component' => 'select', 'validator' => array(), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '欄位', 'dbVariable' => 'fields', 'frontendVariable' => 'fields', 'component' => 'datasetfields', 'validator' => array(), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
    )
);

/**
 * 用戶群組資料集
 */
$config['master']['dataset']['group'] = array(
    'name'              => '用戶群組資料集',
    'metadata_type'     => 'group',
    'fields'        => array(
        array('name' => '代碼', 'dbVariable' => 'code', 'frontendVariable' => 'code', 'component' => 'text', 'validator' => array('required', 'length{3,20}'), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '名稱', 'dbVariable' => 'name', 'frontendVariable' => 'name', 'component' => 'text', 'validator' => array('required', 'length{3,10}'), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
    )
);

/**
 * 資料資料集
 */
$config['master']['dataset']['dataset'] = array(
    'name'              => '資料資料集',
    'metadata_type'     => 'dataset',
    'fields'        => array(
        array('name' => '代碼', 'dbVariable' => 'code', 'frontendVariable' => 'code', 'component' => 'text', 'validator' => array('required', 'length{2,20}'), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '名稱', 'dbVariable' => 'name', 'frontendVariable' => 'name', 'component' => 'text', 'validator' => array('required', 'length{2,10}'), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '欄位', 'dbVariable' => 'fields', 'frontendVariable' => 'fields', 'component' => 'datasetfields', 'validator' => array(), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
    )
);


/**
 * 組件資料集
 */
$config['master']['dataset']['widget'] = array(
    'name'              => '組件資料集',
    'metadata_type'     => 'widget',
    'fields'        => array(
        array('name' => '代碼', 'dbVariable' => 'code', 'frontendVariable' => 'code', 'component' => 'text', 'validator' => array('required', 'length{3,20}'), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '名稱', 'dbVariable' => 'name', 'frontendVariable' => 'name', 'component' => 'text', 'validator' => array('required', 'length{3,10}'), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '引用資料集', 'dbVariable' => 'dataset', 'frontendVariable' => 'dataset', 'component' => 'datasetlist', 'validator' => array(), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '組件', 'dbVariable' => 'setting', 'frontendVariable' => 'setting', 'component' => 'widgetsetting', 'validator' => array(), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
    )
);


/**
 * 管理者資料集
 */
$config['master']['dataset']['account'] = array(
    'name'              => '管理者資料集',
    'metadata_type'     => 'account',
    'fields'        => array(
        array('name' => '姓名', 'dbVariable' => 'name', 'frontendVariable' => 'name', 'component' => 'text', 'validator' => array('required', 'length{2,10}'), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '帳號', 'dbVariable' => 'account', 'frontendVariable' => 'account', 'component' => 'text', 'validator' => array('required', 'length{3,30}'), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '密碼', 'dbVariable' => 'password', 'frontendVariable' => 'password', 'component' => 'text', 'validator' => array('required', 'length{5,30}'), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
    )
);

/**
 * 系統資訊資料集
 */
$config['master']['dataset']['information'] = array(
    'name'              => '登入資料集',
    'metadata_type'     => 'information',
    'fields'        => array(
        array('name' => '系統名稱', 'dbVariable' => 'title', 'frontendVariable' => 'title', 'component' => 'text', 'validator' => array('required', 'system.length{3,20}'), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
    )
);



/**
 * 頁面資料集
 */
$config['master']['dataset']['page'] = array(
    'name'              => '後台頁面資料集',
    'metadata_type'     => 'page',
    'fields'        => array(
        array('name' => '代碼', 'dbVariable' => 'code', 'frontendVariable' => 'code', 'component' => 'text', 'validator' => array(), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '名稱', 'dbVariable' => 'name', 'frontendVariable' => 'name', 'component' => 'text', 'validator' => array(), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '路徑', 'dbVariable' => 'uri', 'frontendVariable' => 'uri', 'component' => 'text', 'validator' => array(), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '主題', 'dbVariable' => 'theme', 'frontendVariable' => 'theme', 'component' => 'text', 'validator' => array(), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '必需登入', 'dbVariable' => 'isLogin', 'frontendVariable' => 'isLogin', 'component' => 'switch', 'validator' => array('enum{Y,N}'), 'converter' => array('selectOne{Y:是,N:否}'), 'source' => '["是","否"]', 'fieldTip' => ''),
        array('name' => '所屬群組', 'dbVariable' => 'group', 'frontendVariable' => 'group', 'component' => 'select', 'validator' => array(), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '組件', 'dbVariable' => 'widgets', 'frontendVariable' => 'widgets', 'component' => 'widgets', 'validator' => array(), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
    )
);

/**
 * 頁面資料集
 */
$config['master']['dataset']['front_page'] = array(
    'name'              => '前台頁面資料集',
    'metadata_type'     => 'front_page',
    'fields'        => array(
        array('name' => '代碼', 'dbVariable' => 'code', 'frontendVariable' => 'code', 'component' => 'text', 'validator' => array(), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '名稱', 'dbVariable' => 'name', 'frontendVariable' => 'name', 'component' => 'text', 'validator' => array(), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '路徑', 'dbVariable' => 'uri', 'frontendVariable' => 'uri', 'component' => 'text', 'validator' => array(), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '主題', 'dbVariable' => 'theme', 'frontendVariable' => 'theme', 'component' => 'text', 'validator' => array(), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '必需登入', 'dbVariable' => 'isLogin', 'frontendVariable' => 'isLogin', 'component' => 'switch', 'validator' => array('enum{Y,N}'), 'converter' => array('selectOne{Y:是,N:否}'), 'source' => '["是","否"]', 'fieldTip' => ''),
        array('name' => '所屬群組', 'dbVariable' => 'group', 'frontendVariable' => 'group', 'component' => 'select', 'validator' => array(), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
        array('name' => '組件', 'dbVariable' => 'widgets', 'frontendVariable' => 'widgets', 'component' => 'widgets', 'validator' => array(), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
    )
);

/**
 * 版面資料集
 */
$config['master']['dataset']['template'] = array(
    'name'              => '版面資料集',
    'metadata_type'     => 'template',
    'fields'        => array(
        array('name' => '組件', 'dbVariable' => 'widgets', 'frontendVariable' => 'widgets', 'component' => 'widgets', 'validator' => array(), 'converter' => array(), 'source' => '', 'fieldTip' => ''),
    )
);

/*************************************
 *               組件
 *************************************/

/**
 * 選單組件
 */
$config['master']['widget']['menu'] = array(
    'name'              => '選單',
    'code'              => 'menu',
    'dataset'          => '',
    'setting'            => array(
        'code'  => 'menu',
        'menu'   => array(
            // 一層
            array('type' => 'pageClass', 'icon' => 'fas fa-cogs', 'title' => '系統設定', 'subItems' => array(
                array('type' => 'page', 'icon' => 'fas fa-sign-in-alt', 'title' => '系統資訊', 'uri' => 'master/information'),
                array('type' => 'page', 'icon' => 'fas fa-envelope', 'title' => '信箱管理', 'uri' => 'master/email'),
                array('type' => 'page', 'icon' => 'fas fa-users', 'title' => '管理者管理', 'uri' => 'master/account'),
            )),

            array('type' => 'pageClass', 'icon' => 'fas fa-user', 'title' => '用戶管理', 'subItems' => array(
                array('type' => 'page', 'icon' => 'fas fa-user', 'title' => '用戶管理', 'uri' => 'master/user'),
                array('type' => 'page', 'icon' => 'fas fa-users', 'title' => '群組管理', 'uri' => 'master/group'),
            )),

            array('type' => 'pageClass', 'icon' => 'fas fa-info', 'title' => '資料', 'subItems' => array(
                array('type' => 'page', 'icon' => 'fas fa-database', 'title' => '資料管理', 'uri' => 'master/dataset'),
                array('type' => 'page', 'icon' => 'far fa-window-restore', 'title' => '組件管理', 'uri' => 'master/widget'),
            )),


            array('type' => 'pageClass', 'icon' => 'far fa-window-maximize', 'title' => '後台版面', 'subItems' => array(
                array('type' => 'page', 'icon' => 'far fa-window-maximize', 'title' => '頁面管理', 'uri' => 'master/page'),
                array('type' => 'page', 'icon' => 'far fa-window-maximize', 'title' => '頁頭管理', 'uri' => 'master/header'),
                array('type' => 'page', 'icon' => 'far fa-window-maximize', 'title' => '頁尾管理', 'uri' => 'master/footer'),
                array('type' => 'page', 'icon' => 'far fa-window-maximize', 'title' => '左側欄管理', 'uri' => 'master/leftside'),
                array('type' => 'page', 'icon' => 'far fa-window-maximize', 'title' => '右側欄管理', 'uri' => 'master/rightside'),
            )),

            array('type' => 'pageClass', 'icon' => 'far fa-window-maximize', 'title' => '前台版面', 'subItems' => array(
                array('type' => 'page', 'icon' => 'far fa-window-maximize', 'title' => '頁面管理', 'uri' => 'master/front/page'),
                array('type' => 'page', 'icon' => 'far fa-window-maximize', 'title' => '頁頭管理', 'uri' => 'master/front/header'),
                array('type' => 'page', 'icon' => 'far fa-window-maximize', 'title' => '頁尾管理', 'uri' => 'master/front/footer'),
                array('type' => 'page', 'icon' => 'far fa-window-maximize', 'title' => '左側欄管理', 'uri' => 'master/front/leftside'),
                array('type' => 'page', 'icon' => 'far fa-window-maximize', 'title' => '右側欄管理', 'uri' => 'master/front/rightside'),
            )),


            /*
            // 二層
            array('type' => 'pageClass', 'icon' => '', 'title' => '系統管理', 'subItems' => array(
                array('type' => 'page', 'icon' => '', 'title' => '登入設定', 'code' => 'login'),
                array('type' => 'page', 'icon' => '', 'title' => '信箱管理', 'code' => 'email'),
            )),
    
            // 三層
            array('type' => 'pageClass', 'icon' => '', 'title' => '系統設定',  'subItems' => array(
                array('type' => 'pageClass', 'icon' => '', 'title' => '網站管理',  'subItems' => array(
                    array('type' => 'page', 'icon' => '', 'title' => '登入設定', 'code' => 'login'),
                    array('type' => 'page', 'icon' => '', 'title' => '信箱管理', 'code' => 'email'),
                )),
                array('type' => 'pageClass', 'icon' => '', 'title' => '不要管理', 'subItems' => array(
                    array('type' => 'page', 'icon' => '', 'title' => '登入設定', 'code' => 'login'),
                    array('type' => 'page', 'icon' => '', 'title' => '信箱管理', 'code' => 'email'),
                )),
            )),
    */
        )
    ),

);

/**
 * 登入組件
 */
$config['master']['widget']['login'] = array(
    'name'              => '登入組件',
    'code'              => 'login',
    'dataset'          => '',
    'setting'            => array(
        'code'  => 'login',
        'login_event' => 'api/master_login',
    ),
);

/**
 * 登入設定
 */
$config['master']['widget']['information'] = array(
    'name'              => '登入設定',
    'code'              => 'information',
    'dataset'          => 'information',
    'setting'            => array(
        'code'  => 'form',
        'submit_event' => 'api/metadata/information/information',
        'data_event' => 'api/metadata/information/information'
    ),
);

/**
 * 信箱管理組件
 */
$config['master']['widget']['email'] = array(
    'name'              => '信箱管理',
    'code'              => 'email',
    'dataset'           => 'email',
    'setting'            => array(
        'code'  => 'data',
        'permission' => array('ADD', 'MODIFY', 'DELETE'),
        'listfields' => array(
            'code' => array('status' => 'Y', 'name' => '代碼'),
            'title' => array('status' => 'Y', 'name' => '用述')
        ),
        'classLevelCount' => 0,
        'deletes_event' => '',
        'datas_event' => 'api/metadatas/email',
        'data_event' => 'api/metadata/email',
        'add_event' => 'api/metadata/email',
        'modify_event' => 'api/metadata/email',
        'delete_event' => 'api/metadata/email',
        'sort_event'    => 'api/metadatas/email',
    ),
);

/**
 * 帳號管理組件
 */
$config['master']['widget']['account'] = array(
    'name'              => '帳號管理',
    'code'              => 'account',
    'dataset'           => 'account',
    'setting'            => array(
        'code'  => 'data',
        'permission' => array('ADD', 'MODIFY', 'DELETE'),
        'listfields' => array(
            'name' => array('status' => 'Y', 'name' => '姓名'),
            'account' => array('status' => 'Y', 'name' => '帳號')
        ),
        'classLevelCount' => 0,
        'deletes_event' => '',
        'datas_event' => 'api/metadatas/account',
        'data_event' => 'api/metadata/account',
        'add_event' => 'api/metadata/account',
        'modify_event' => 'api/metadata/account',
        'delete_event' => 'api/metadata/account',
        'sort_event'    => 'api/metadatas/account',
    ),
);

/**
 * 用戶管理組件
 */
$config['master']['widget']['user'] = array(
    'name'              => '用戶管理',
    'code'              => 'user',
    'dataset'           => 'user',
    'setting'            => array(
        'code'  => 'data',
        'permission' => array('ADD', 'MODIFY', 'DELETE'),
        'listfields' => array(
            'code' => array('status' => 'Y', 'name' => '代碼'),
            'name' => array('status' => 'Y', 'name' => '名稱')
        ),
        'classLevelCount' => 0,
        'deletes_event' => '',
        'datas_event' => 'api/metadatas/user',
        'data_event' => 'api/metadata/user',
        'add_event' => 'api/metadata/user',
        'modify_event' => 'api/metadata/user',
        'delete_event' => 'api/metadata/user',
        'sort_event'    => 'api/metadatas/user',
    ),
);

/**
 * 群組管理組件
 */
$config['master']['widget']['group'] = array(
    'name'              => '群組管理',
    'code'              => 'group',
    'dataset'           => 'group',
    'setting'            => array(
        'code'  => 'data',
        'permission' => array('ADD', 'MODIFY', 'DELETE'),
        'listfields' => array(
            'code' => array('status' => 'Y', 'name' => '代碼'),
            'name' => array('status' => 'Y', 'name' => '名稱')
        ),
        'classLevelCount' => 0,
        'deletes_event' => '',
        'datas_event' => 'api/metadatas/group',
        'data_event' => 'api/metadata/group',
        'add_event' => 'api/metadata/group',
        'modify_event' => 'api/metadata/group',
        'delete_event' => 'api/metadata/group',
        'sort_event'    => 'api/metadatas/group',
    ),
);

/**
 * 資料管理組件
 */
$config['master']['widget']['dataset'] = array(
    'name'              => '資料管理',
    'code'              => 'dataset',
    'dataset'           => 'dataset',
    'setting'            => array(
        'code'  => 'data',
        'permission' => array('ADD', 'MODIFY', 'DELETE'),
        'listfields' => array(
            'code' => array('status' => 'Y', 'name' => '代碼'),
            'name' => array('status' => 'Y', 'name' => '名稱')
        ),
        'classLevelCount' => 0,
        'deletes_event' => '',
        'datas_event' => 'api/metadatas/dataset',
        'data_event' => 'api/metadata/dataset',
        'add_event' => 'api/dataset',
        'modify_event' => 'api/dataset',
        'delete_event' => 'api/dataset',
        'sort_event'    => 'api/metadatas/dataset',
    ),
);

/**
 * 組件管理組件
 */
$config['master']['widget']['widget'] = array(
    'name'              => '組件管理',
    'code'              => 'widget',
    'dataset'           => 'widget',
    'setting'            => array(
        'code'  => 'data',
        'permission' => array('ADD', 'MODIFY', 'DELETE'),
        'listfields' => array(
            'code' => array('status' => 'Y', 'name' => '代碼'),
            'name' => array('status' => 'Y', 'name' => '名稱')
        ),
        'classLevelCount' => 0,
        'deletes_event' => '',
        'datas_event' => 'api/metadatas/widget',
        'data_event' => 'api/metadata/widget',
        'add_event' => 'api/metadata/widget',
        'modify_event' => 'api/metadata/widget',
        'delete_event' => 'api/metadata/widget',
        'sort_event'    => 'api/metadatas/widget',
    ),
);

/**
 * 後台頁頭管理
 */
$config['master']['widget']['header'] = array(
    'name'              => '後台頁頭管理',
    'code'              => 'header',
    'dataset'          => 'template',
    'setting'            => array(
        'code'  => 'form',
        'data_event' => 'api/metadata/template/header',
        'submit_event' => 'api/metadata/template/header',
        'cancel_event' => '',
    ),
);

/**
 * 後台頁面管理
 */
$config['master']['widget']['page'] = array(
    'name'              => '後台頁面管理',
    'code'              => 'page',
    'dataset'          => 'page',
    'setting'            => array(
        'code'  => 'data',
        'permission' => array('ADD', 'MODIFY', 'DELETE', 'SORT'),
        'listfields' => array(
            'code' => array('status' => 'Y', 'name' => '頁面代碼'),
            'name' => array('status' => 'Y', 'name' => '頁面名稱')
        ),
        'deletes_event' => '',
        'datas_event' => 'api/metadatas/page',
        'data_event' => 'api/metadata/page',
        'add_event' => 'api/metadata/page',
        'modify_event' => 'api/metadata/page',
        'delete_event' => 'api/metadata/page',
        'sort_event'    => 'api/metadatas/page',
    ),
);

/**
 * 後台左側欄管理
 */
$config['master']['widget']['leftside'] = array(
    'name'              => '後台左側欄管理',
    'code'              => 'leftside',
    'dataset'          => 'template',
    'setting'            => array(
        'code'  => 'form',
        'data_event' => 'api/metadata/template/leftside',
        'submit_event' => 'api/metadata/template/leftside',
        'cancel_event' => '',
    ),
);

/**
 * 後台右側欄管理
 */
$config['master']['widget']['rightside'] = array(
    'name'              => '後台右側欄管理',
    'code'              => 'rightside',
    'dataset'          => 'template',
    'setting'            => array(
        'code'  => 'form',
        'data_event' => 'api/metadata/template/rightside',
        'submit_event' => 'api/metadata/template/rightside',
        'cancel_event' => '',
    ),
);

/**
 * 後台頁尾管理
 */
$config['master']['widget']['footer'] = array(
    'name'              => '後台頁尾管理',
    'code'              => 'footer',
    'dataset'          => 'template',
    'setting'            => array(
        'code'  => 'form',
        'data_event' => 'api/metadata/template/footer',
        'submit_event' => 'api/metadata/template/footer',
        'cancel_event' => '',
    ),
);

/**
 * 頁尾組件
 */
$config['master']['widget']['footertext'] = array(
    'name'              => '頁尾文字',
    'code'              => 'footertext',
    'dataset'          => '',
    'setting'            => array(
        'code'  => 'footer'
    ),
);




/**
 * 前台頁頭組件
 */
$config['master']['widget']['front_header'] = array(
    'name'              => '前台頁頭管理',
    'code'              => 'front_header',
    'dataset'          => 'template',
    'setting'            => array(
        'code'  => 'form',
        'data_event' => 'api/metadata/template/front_header',
        'submit_event' => 'api/metadata/template/front_header',
        'cancel_event' => '',
    ),
);

/**
 * 前台頁面管理
 */
$config['master']['widget']['front_page'] = array(
    'name'              => '前台頁面管理',
    'code'              => 'front_page',
    'dataset'          => 'page',
    'setting'            => array(
        'code'  => 'data',
        'permission' => array('ADD', 'MODIFY', 'DELETE'),
        'listfields' => array(
            'code' => array('status' => 'Y', 'name' => '頁面代碼'),
            'name' => array('status' => 'Y', 'name' => '頁面名稱')
        ),
        'deletes_event' => '',
        'datas_event' => 'api/metadatas/front_page',
        'data_event' => 'api/metadata/front_page',
        'add_event' => 'api/metadata/front_page',
        'modify_event' => 'api/metadata/front_page',
        'delete_event' => 'api/metadata/front_page',
        'sort_event'    => 'api/metadatas/front_page',
    ),
);

/**
 * 前台左側欄管理
 */
$config['master']['widget']['front_leftside'] = array(
    'name'              => '前台左側欄管理',
    'code'              => 'front_leftside',
    'dataset'          => 'template',
    'setting'            => array(
        'code'  => 'form',
        'data_event' => 'api/metadata/template/front_leftside',
        'submit_event' => 'api/metadata/template/front_leftside',
        'cancel_event' => '',
    ),
);

/**
 * 前台右側欄管理
 */
$config['master']['widget']['front_rightside'] = array(
    'name'              => '前台右側欄管理',
    'code'              => 'front_rightside',
    'dataset'          => 'template',
    'setting'            => array(
        'code'  => 'form',
        'data_event' => 'api/metadata/template/front_rightside',
        'submit_event' => 'api/metadata/template/front_rightside',
        'cancel_event' => '',
    ),
);

/**
 * 前台頁尾管理
 */
$config['master']['widget']['front_footer'] = array(
    'name'              => '前台頁尾管理',
    'code'              => 'front_footer',
    'dataset'          => 'template',
    'setting'            => array(
        'code'  => 'form',
        'data_event' => 'api/metadata/template/front_footer',
        'submit_event' => 'api/metadata/template/front_footer',
        'cancel_event' => '',
    ),
);


/*************************************
 *               版面
 *************************************/

// 頁頭版面
$config['master']['template']['header'] = array(
    'name'      => '頁頭',
    'code'      => 'header',
    'widgets'   => array(
        array('code' => 'notify', 'desktop' => 12, 'pad' => 12, 'mobile' => 12),
    )
);

// 頁尾版面
$config['master']['template']['footer'] = array(
    'name'      => '頁尾',
    'code'      => 'footer',
    'widgets'   => array(
        array('code' => 'footertext', 'desktop' => 12, 'pad' => 12, 'mobile' => 12),
    )
);

// 左側版面
$config['master']['template']['leftside'] = array(
    'name'      => '左側版面',
    'code'      => 'leftside',
    'widgets'   => array(
        array('code' => 'menu', 'desktop' => 12, 'pad' => 12, 'mobile' => 12),
    )
);

// 右側版面
$config['master']['template']['rightside'] = array(
    'name'      => '右側版面',
    'code'      => 'rightside',
    'widgets'   => array()
);


/*************************************
 *               頁面
 *************************************/
/**
 * 登入頁面
 */
$config['master']['page']['login'] = array(
    'name'      => '登入頁面',
    'code'      => 'login',
    'uri'      => 'master/login',
    'theme'     => 'login/adminlte',
    'widgets'   => array(
        array('code' => 'login', 'desktop' => 12, 'pad' => 12, 'mobile' => 12),
    )
);

/**
 * 系統設定
 */
$config['master']['page']['information'] = array(
    'name'      => '系統設定',
    'code'      => 'information',
    'uri'      => 'master/information',
    'theme'     => 'page/adminlte',
    'widgets'   => array(
        array('code' => 'information', 'desktop' => 12, 'pad' => 12, 'mobile' => 12),
    )
);

/**
 * 信箱管理
 */
$config['master']['page']['email'] = array(
    'name'      => '信箱管理',
    'code'      => 'email',
    'uri'      => 'master/email',
    'widgets'   => array(
        array('code' => 'email', 'desktop' => 12, 'pad' => 12, 'mobile' => 12),
    )
);

/**
 * 帳號管理
 */
$config['master']['page']['account'] = array(
    'name'      => '帳號管理',
    'code'      => 'account',
    'uri'      => 'master/account',
    'widgets'   => array(
        array('code' => 'account', 'desktop' => 12, 'pad' => 12, 'mobile' => 12),
    )
);

/**
 * 用戶管理
 */
$config['master']['page']['user'] = array(
    'name'      => '用戶管理',
    'code'      => 'user',
    'uri'      => 'master/user',
    'widgets'   => array(
        array('code' => 'user', 'desktop' => 12, 'pad' => 12, 'mobile' => 12),
    )
);

/**
 * 群組管理
 */
$config['master']['page']['group'] = array(
    'name'      => '群組管理',
    'code'      => 'group',
    'uri'      => 'master/group',
    'widgets'   => array(
        array('code' => 'group', 'desktop' => 12, 'pad' => 12, 'mobile' => 12),
    )
);

/**
 * 資料管理
 */
$config['master']['page']['dataset'] = array(
    'name'      => '資料管理',
    'code'      => 'dataset',
    'uri'       => 'master/dataset',
    'widgets'   => array(
        array('code' => 'dataset', 'desktop' => 12, 'pad' => 12, 'mobile' => 12),
    )
);

/**
 * 組件管理
 */
$config['master']['page']['widget'] = array(
    'name'      => '組件管理',
    'code'      => 'widget',
    'uri'       => 'master/widget',
    'widgets'   => array(
        array('code' => 'widget', 'desktop' => 12, 'pad' => 12, 'mobile' => 12),
    )
);


/**
 * 後台頁頭管理
 */
$config['master']['page']['header'] = array(
    'name'      => '後台頁頭管理',
    'code'      => 'header',
    'uri'       => 'master/header',
    'widgets'   => array(
        array('code' => 'header', 'desktop' => 12, 'pad' => 12, 'mobile' => 12),
    )
);


/**
 * 後台頁面管理
 */
$config['master']['page']['content'] = array(
    'name'      => '後台頁面管理',
    'code'      => 'content',
    'uri'       => 'master/page',
    'widgets'   => array(
        array('code' => 'page', 'desktop' => 12, 'pad' => 12, 'mobile' => 12),
    )
);


/**
 * 後台左側欄管理
 */
$config['master']['page']['leftside'] = array(
    'name'      => '後台左側欄管理',
    'code'      => 'leftside',
    'uri'       => 'master/leftside',
    'widgets'   => array(
        array('code' => 'leftside', 'desktop' => 12, 'pad' => 12, 'mobile' => 12),
    )
);


/**
 * 後台右側欄管理
 */
$config['master']['page']['rightside'] = array(
    'name'      => '後台右側欄管理',
    'code'      => 'rightside',
    'uri'       => 'master/rightside',
    'widgets'   => array(
        array('code' => 'rightside', 'desktop' => 12, 'pad' => 12, 'mobile' => 12),
    )
);


/**
 * 後台頁尾管理
 */
$config['master']['page']['footer'] = array(
    'name'      => '後台頁尾管理',
    'code'      => 'footer',
    'uri'       => 'master/footer',
    'widgets'   => array(
        array('code' => 'footer', 'desktop' => 12, 'pad' => 12, 'mobile' => 12),
    )
);


/**
 * 前台頁頭管理
 */
$config['master']['page']['front_header'] = array(
    'name'      => '前台頁頭管理',
    'code'      => 'front_header',
    'uri'       => 'master/front/header',
    'widgets'   => array(
        array('code' => 'front_header', 'desktop' => 12, 'pad' => 12, 'mobile' => 12),
    )
);


/**
 * 前台頁面管理
 */
$config['master']['page']['front_content'] = array(
    'name'      => '前台頁面管理',
    'code'      => 'front_content',
    'uri'       => 'master/front/page',
    'widgets'   => array(
        array('code' => 'front_page', 'desktop' => 12, 'pad' => 12, 'mobile' => 12),
    )
);


/**
 * 前台左側欄管理
 */
$config['master']['page']['front_leftside'] = array(
    'name'      => '前台左側欄管理',
    'code'      => 'front_leftside',
    'uri'       => 'master/front/leftside',
    'widgets'   => array(
        array('code' => 'front_leftside', 'desktop' => 12, 'pad' => 12, 'mobile' => 12),
    )
);


/**
 * 前台右側欄管理
 */
$config['master']['page']['front_rightside'] = array(
    'name'      => '前台右側欄管理',
    'code'      => 'front_rightside',
    'uri'       => 'master/front/rightside',
    'widgets'   => array(
        array('code' => 'front_rightside', 'desktop' => 12, 'pad' => 12, 'mobile' => 12),
    )
);


/**
 * 前台頁尾管理
 */
$config['master']['page']['front_footer'] = array(
    'name'      => '前台頁尾管理',
    'code'      => 'front_footer',
    'uri'       => 'master/front/footer',
    'widgets'   => array(
        array('code' => 'front_footer', 'desktop' => 12, 'pad' => 12, 'mobile' => 12),
    )
);