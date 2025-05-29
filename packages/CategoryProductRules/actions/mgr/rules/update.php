<?php

if (!function_exists('updateRules')) {
    function updateRules($category_id, $context_key, $data)
    {
        global $modx;
        $category_id = (int)$category_id;

        $table_prefix = $modx->getOption('table_prefix');
        $res = $modx->query("UPDATE {$table_prefix}catprod_rules 
                            SET `context_key` = '$context_key', `rules` = '$data'
                            WHERE `category_id` = $category_id");
        if (!$res) {
            exit(json_encode([
                'success' => false,
                'data' => "Error update"
            ]));
        } else {
            exit(json_encode([
                'success' => true,
                'data' => ""
            ]));
        }
    }
}
