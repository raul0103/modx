<?php

if (!function_exists('tsFetchContextCategories')) {
    function tsFetchContextCategories()
    {
        global $modx;

        $table_prefix = $modx->getOption('table_prefix');
        $res = $modx->query("SELECT * FROM {$table_prefix}ts_category_tags");
        $categories = $res->fetchAll(PDO::FETCH_ASSOC);

        $data_context = [];
        foreach ($categories as $category) {
            $ctx = $category['context_key'];
            if (!isset($data_context[$ctx])) $data_context[$ctx] = [];

            $data_context[$ctx][] = $category;
        }

        $ctxs = $modx->getCollection('modContext');
        foreach ($ctxs as $ctx) {
            if (!isset($data_context[$ctx->key])) {
                $data_context[$ctx->key] = [];
            }
        }

        $result = [];
        foreach ($data_context as $context_key => $categoiries) {
            $result[] = [
                "context_key" => $context_key,
                "categories" => $categoiries
            ];
        }

        exit(json_encode([
            "success" => true,
            "data" => $result
        ]));
    }
}
