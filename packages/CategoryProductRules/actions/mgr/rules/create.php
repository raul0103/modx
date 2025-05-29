<?php

if (!function_exists('createRules')) {
    function createRules($category_id, $context_key, $data)
    {
        global $modx;
        $category_id = (int)$category_id;

        $table_prefix = $modx->getOption('table_prefix');
        $res = $modx->query("INSERT INTO {$table_prefix}catprod_rules (`category_id`,`context_key`,`rules`) 
                        VALUES ($category_id,'$context_key','$data')");
        if (!$res) {
            exit(json_encode([
                'success' => false,
                'data' => "Error insert"
            ]));
        } else {
            exit(json_encode([
                'success' => true,
                'data' => ""
            ]));
        }
    }
}
