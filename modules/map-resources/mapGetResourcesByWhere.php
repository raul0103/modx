<?php

/**
 * Скрипт отдает одномерный массив ресурсов найденных по where
 * 
 * @param $where - JSON
 * @param $data - массив данных
 */

if (empty($data) || empty($where)) return;

// if (gettype($ids) == 'string')
//     $ids = explode(',', $ids);

$where = json_decode($where, true);

if (!function_exists('filterResources')) {
    function filterResources($data, $where)
    {
        $result = [];

        $handler = function ($items) use (&$handler, &$result, $where) {

            foreach ($items as $item) {
                $condition_count = count($where);
                $condition_founds = 0;
                foreach ($where as $key => $value) {
                    if ($item[$key] == $value) {
                        $condition_founds++;
                    }
                }
                if ($condition_count == $condition_founds) {
                    $result[] = $item;
                }

                // Если есть дочерние элементы, обрабатываем их
                if (isset($item['children']) && is_array($item['children'])) {
                    $handler($item['children']);
                }
            }
        };

        // Заполняем мапу
        $handler($data);

        return $result;
    }
}


$cache_name = md5(serialize($scriptProperties));
$cache_options = [
    xPDO::OPT_CACHE_KEY => 'default/map-resources/mapGetResourcesByWhere/' . $modx->resource->context_key . '/',
];

if (!$output = $modx->cacheManager->get($cache_name, $cache_options)) {
    $output = filterResources($data, $where);
    $modx->cacheManager->set($cache_name, $output, 0, $cache_options);
}

return $output;
