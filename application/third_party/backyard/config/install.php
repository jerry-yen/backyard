<?php

/**
 * 新增模組資料表語法
 */
$config['install']['metadata'] = '
    CREATE TABLE IF NOT EXISTS byard_metadata (
        id VARCHAR(40) NOT NULL PRIMARY KEY COMMENT "識別碼" COLLATE utf8_unicode_ci,
        metadata_type VARCHAR(30) NOT NULL COMMENT "設定類型(login,dataset,email,widget,page,menu,...)" COLLATE utf8_unicode_ci,
        code VARCHAR(30) NOT NULL COMMENT "代碼" COLLATE utf8_unicode_ci,
        metadata TEXT NULL COMMENT "後設資料(JSON)" COLLATE utf8_unicode_ci,
        created_at DATETIME COMMENT "建置時間" COLLATE utf8_unicode_ci,
        updated_at DATETIME COMMENT "更新時間" COLLATE utf8_unicode_ci,
        KEY `code_index` (`code`),
        KEY `created_at_index` (`created_at`),
        KEY `updated_at_index` (`updated_at`)
    ) CHARACTER SET utf8 COLLATE utf8_unicode_ci;
';

/**
 * 新增檔案資料表語法
 */
$config['install']['file'] = '
    CREATE TABLE IF NOT EXISTS byard_file (
        id VARCHAR(40) NOT NULL PRIMARY KEY COMMENT "識別碼" COLLATE utf8_unicode_ci,
        own_id VARCHAR(40) NOT NULL COMMENT "擁有者識別碼" COLLATE utf8_unicode_ci,
        own_field VARCHAR(40) NOT NULL COMMENT "擁有者欄位名稱" COLLATE utf8_unicode_ci,
        name VARCHAR(255) NOT NULL COMMENT "檔案名稱" COLLATE utf8_unicode_ci,
        ext VARCHAR(10) NOT NULL COMMENT "副檔名" COLLATE utf8_unicode_ci,
        file_type VARCHAR(30) NOT NULL COMMENT "檔案類型(ex: image/png)" COLLATE utf8_unicode_ci,
        path VARCHAR(255) NOT NULL COMMENT "檔案路徑" COLLATE utf8_unicode_ci,
        file_size INT(11) NOT NULL COMMENT "檔案大小" COLLATE utf8_unicode_ci,
        created_at DATETIME COMMENT "建置時間" COLLATE utf8_unicode_ci,
        updated_at DATETIME COMMENT "更新時間" COLLATE utf8_unicode_ci,
        sorted_at DATETIME NULL COMMENT "排序時間" COLLATE utf8_unicode_ci,
        sequence INT(11) NULL COMMENT "排列順序" COLLATE utf8_unicode_ci,
        top_at DATETIME NULL COMMENT "置頂時間" COLLATE utf8_unicode_ci,
        KEY `own_id_index` (`own_id`),
        KEY `own_field_index` (`own_field`),
        KEY `created_at_index` (`created_at`),
        KEY `updated_at_index` (`updated_at`),
        KEY `sorted_at_index` (`sorted_at`),
        KEY `sequence_index` (`sequence`),
        KEY `top_at_index` (`top_at`)
    ) CHARACTER SET utf8 COLLATE utf8_unicode_ci;
';

/**
 * 新增關連資料表語法
 */
$config['install']['relation'] = '
    CREATE TABLE IF NOT EXISTS byard_relation (
        id VARCHAR(40) NOT NULL PRIMARY KEY COMMENT "識別碼" COLLATE utf8_unicode_ci,
        source_id VARCHAR(40) NOT NULL COMMENT "來源記錄識別碼" COLLATE utf8_unicode_ci,
        source_field_variable VARCHAR(30) NULL COMMENT "來源欄位名稱" COLLATE utf8_unicode_ci,
        target_id VARCHAR(40) NOT NULL COMMENT "目標記錄識別碼" COLLATE utf8_unicode_ci,
        target_field_variable VARCHAR(30) NULL COMMENT "目標欄位名稱" COLLATE utf8_unicode_ci,
        created_at DATETIME COMMENT "建置時間" COLLATE utf8_unicode_ci,
        updated_at DATETIME COMMENT "更新時間" COLLATE utf8_unicode_ci,
        KEY `source_id_index` (`source_id`),
        KEY `source_field_variable_index` (`source_field_variable`),
        KEY `target_id_index` (`target_id`),
        KEY `target_field_variable_index` (`target_field_variable`),
        KEY `created_at_index` (`created_at`),
        KEY `updated_at_index` (`updated_at`)
    ) CHARACTER SET utf8 COLLATE utf8_unicode_ci;
';