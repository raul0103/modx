<?php

/**
 * Отдает детей переданного родителя
 * 
 * @param $parent_id - ID родительского ресурса
 * @param $data - массив данных
 */

if (empty($data) || empty($parent_id)) return;

if (!function_exists('getChildren')) {
    function getChildren($data, $parent_id)
    {
        foreach ($data as $item) {
            if ($item['id'] == $parent_id) {
                return $item['children'];
            }

            // Если есть дочерние элементы, обрабатываем их
            if (isset($item['children']) && is_array($item['children'])) {
                getChildren($item['children'], $parent_id);
            }
        }

        return [];
    }
}


$cache_name = md5(serialize($scriptProperties));
$cache_options = [
    xPDO::OPT_CACHE_KEY => 'default/map-resources/mapGetChildren/' . $modx->resource->context_key . '/',
];

if (!$output = $modx->cacheManager->get($cache_name, $cache_options)) {
    $output = getChildren($data, $parent_id);
    $modx->cacheManager->set($cache_name, $output, 0, $cache_options);
}

return $output;
