<?php

/**
 * Таблица хранит все правила для каждой категории
 */

$table_prefix = $modx->getOption('table_prefix');
$res = $modx->query("CREATE TABLE {$table_prefix}catprod_rules (
                    category_id   BIGINT NOT NULL,
                    context_key   VARCHAR(255) NOT NULL,
                    rules         JSON NOT NULL,
                PRIMARY KEY (category_id, context_key)
                );");
