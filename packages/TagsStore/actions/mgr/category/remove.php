<?php

if (!function_exists('tsRemoveCategory')) {
    function tsRemoveCategory($category_id)
    {
        global $modx;

        $table_prefix = $modx->getOption('table_prefix');
        $res = $modx->query("DELETE FROM {$table_prefix}ts_category_tags WHERE id = $category_id");
        if (!$res) {
            exit(json_encode([
                "success" => false,
                "message" => "Error delete category"
            ]));
        }

        $modx->query("DELETE FROM {$table_prefix}ts_tag_items WHERE category_id = $category_id");

        exit(json_encode([
            "success" => true
        ]));
    }
}
