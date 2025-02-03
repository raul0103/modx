<?php

/**
 * Скрипт отдает ресурсы переданные в переменой $ids
 * Отдает ресурсы с детьми children
 * 
 * @param $ids - массив ID которые необходимо передать
 * @param $data - массив данных
 */

if (empty($data) || empty($ids)) return;

if (gettype($ids) == 'string')
    $ids = explode(',', $ids);


if (!function_exists('filterResourcesByIds')) {
    function filterResourcesByIds($data, $ids)
    {
        $result = [];
        $items_map = [];

        // Рекурсивная функция для создания мапы id => элемент
        $map_resources = function ($items) use (&$map_resources, &$items_map) {
            foreach ($items as $item) {
                $items_map[$item['id']] = $item;

                // Если есть дочерние элементы, обрабатываем их
                if (isset($item['children']) && is_array($item['children'])) {
                    $map_resources($item['children']);
                }
            }
        };

        // Заполняем мапу
        $map_resources($data);

        // Формируем результат в порядке переданных $ids
        foreach ($ids as $id) {
            if (isset($items_map[$id])) {
                $result[] = $items_map[$id];
            }
        }

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
