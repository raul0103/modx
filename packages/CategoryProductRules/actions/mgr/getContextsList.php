<?php

if (!function_exists('getContextsList')) {
    function getContextsList()
    {
        global $modx;

        $contexts = $modx->getCollection('modContext');

        $result = [];
        foreach ($contexts as $context) {
            $result[] = [
                "key" => $context->key,
                "name" => $context->name
            ];
        }

        exit(json_encode([
            'success' => true,
            'data' => $result
        ]));
    }
}
