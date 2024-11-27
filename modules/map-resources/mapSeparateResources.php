<?php

/**
 * Скрипт отдает ресурсы переданные в переменой $ids
 * Отдает ресурсы с детьми children
 * 
 * @param $ids - массив ID которые необходимо передать
 * @param $data - массив данных
 */

if (empty($data) || empty($ids)) return;

if (!function_exists('filterResourcesByIds')) {
    function filterResourcesByIds($data, $ids)
    {
        $result = [];

        // Рекурсивная функция для поиска ресурсов по ID
        $find_resources = function ($items) use (&$find_resources, &$result, $ids) {
            foreach ($items as $item) {
                if (in_array($item['id'], $ids)) {
                    $result[] = $item;
                }

                // Если есть дочерние элементы, обходим их рекурсивно
                if (isset($item['children']) && is_array($item['children'])) {
                    $find_resources($item['children']);
                }
            }
        };

        // Запускаем поиск
        $find_resources($data);

        return $result;
    }
}

$cache_name = md5(serialize($scriptProperties));
$cache_options = [
    xPDO::OPT_CACHE_KEY => 'default/map-resources/mapSeparateResources/' . $modx->resource->context_key . '/',
];

if (!$output = $modx->cacheManager->get($cache_name, $cache_options)) {
    $output = filterResourcesByIds($data, $ids);
    $modx->cacheManager->set($cache_name, $output, 0, $cache_options);
}

return $output;
