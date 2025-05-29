<?php

if (!function_exists('getRules')) {
    function getRules($category_id)
    {
        global $modx;
        $category_id = (int)$category_id;

        $table_prefix = $modx->getOption('table_prefix');
        $result = $modx->query("SELECT * FROM {$table_prefix}catprod_rules WHERE category_id = $category_id");
        $data = $result->fetchAll(PDO::FETCH_ASSOC);
        if ($data) {
            exit(json_encode([
                'success' => true,
                'data' => $data[0]
            ]));
        } else {
            exit(json_encode([
                'success' => false,
                'data' => "Error getRules"
            ]));
        }
    }
}
