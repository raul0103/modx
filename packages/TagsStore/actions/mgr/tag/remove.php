<?php

if (!function_exists('tsRemoveTag')) {
    function tsRemoveTag($tag_id)
    {
        global $modx;

        $table_prefix = $modx->getOption('table_prefix');
        $res = $modx->query("DELETE FROM {$table_prefix}ts_tag_items WHERE id = $tag_id");
        if (!$res) {
            exit(json_encode([
                "success" => false,
                "message" => "Error delete tag"
            ]));
        }

        exit(json_encode([
            "success" => true
        ]));
    }
}
