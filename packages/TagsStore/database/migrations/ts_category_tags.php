<?php

/**
 * Таблица категории тегов для админки
 */

$table_prefix = $modx->getOption('table_prefix');
$res = $modx->query("CREATE TABLE {$table_prefix}ts_category_tags (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name varchar(200),
                context_key varchar(200)
                );");
