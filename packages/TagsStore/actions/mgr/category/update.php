<?php

if (!function_exists('tsUpdateCategory')) {
    function tsUpdateCategory($category_id, $name)
    {
        global $modx;

        $table_prefix = $modx->getOption('table_prefix');
        $res = $modx->query("UPDATE {$table_prefix}ts_category_tags SET `name` = '$name' WHERE id = $category_id");
        if (!$res) {
            exit(json_encode([
                "success" => false,
                "message" => "Error update category"
            ]));
        }

        $stmt = $modx->prepare("SELECT * FROM {$table_prefix}ts_category_tags WHERE id = ?");
        $stmt->execute([$category_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        exit(json_encode([
            "success" => true,
            "data" => $row
        ]));
    }
}
