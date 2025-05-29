<?php

if (!function_exists('getCategory')) {
    function getCategory($category_id)
    {
        global $modx;

        $category = $modx->getObject('modResource', $category_id);

        if (!empty($category)) {
            exit(json_encode([
                'success' => true,
                'data' => [
                    'id' => $category->get('id'),
                    'pagetitle' => $category->get('pagetitle'),
                    'context_key' => $category->get('context_key'),
                ]
            ]));
        } else {
            exit(json_encode([
                'success' => false,
                'data' => "Error fetch category"
            ]));
        }
    }
}
