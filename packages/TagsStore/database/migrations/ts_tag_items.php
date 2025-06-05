<?php

/**
 * Таблица тегов
 */

$table_prefix = $modx->getOption('table_prefix');
$res = $modx->query("CREATE TABLE {$table_prefix}ts_tag_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200),
    uri VARCHAR(200) NULL,
    image VARCHAR(200) NULL,
    type ENUM('resource', 'custom') NOT NULL,
    resource_id INT NULL,
    category_id INT,
    group_name VARCHAR(200) NULL
);");
