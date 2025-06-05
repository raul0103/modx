<?php

if (!function_exists('tsFetchCategoryTags')) {
    function tsFetchCategoryTags($category_id)
    {
        global $modx;

        $table_prefix = $modx->getOption('table_prefix');
        $res = $modx->query("SELECT * FROM {$table_prefix}ts_tag_items WHERE category_id = $category_id");
        $tags = $res->fetchAll(PDO::FETCH_ASSOC);

        exit(json_encode([
            "success" => true,
            "data" => $tags
        ]));
    }
}
