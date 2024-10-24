<?php

if (empty($data)) return;


if (!function_exists('sortByFields')) {
    function sortByFields($items, $sortby)
    {
        $key = array_keys($sortby)[0];
        $order = array_flip($sortby[$key]);

        // Сортируем элементы на текущем уровне массива
        usort($items, function ($a, $b) use ($order, $key) {
            $aOrder = $order[$a[$key]] ?? PHP_INT_MAX;
            $bOrder = $order[$b[$key]] ?? PHP_INT_MAX;
            return $aOrder <=> $bOrder;
        });

        foreach ($items as &$item) {
            if (!empty($item['children'])) {
                $item['children'] = sortByFields($item['children'], $sortby);
            }
        }

        return $items;
    }
}


/**
 * Summary of filterData
 * @param mixed $data
 * @param mixed $where
 * @param mixed $depth - Глубина найденных ресурсов. То есть еще получит дочерние, но уже не применяя условия
 * @return void
 */
// if (!function_exists('filterData')) {
//     function filterData($items, $where, $depth = 0)
//     {
//         return $items;
//     }
// }

// if (!function_exists('depthData')) {
//     function depthData($items, $depth)
//     {
//         return $items;
//     }
// }

$cache_name = md5(serialize($scriptProperties));
$cache_options = [
    xPDO::OPT_CACHE_KEY => 'default/map-resources/' . $modx->resource->context_key . '/',
];

if (!$output = $modx->cacheManager->get($cache_name, $cache_options)) {
    // if (!empty($where)) {
    //     $where = json_decode($where, true);
    //     $data = filterData($data, $where, $depth);
    // }

    // if (!empty($depth)) {
    //     $data = depthData($data, $depth);
    // }

    if (!empty($sortby)) {
        $sortby = json_decode($sortby, true);
        $output = sortByFields($data, $sortby);
    }

    $modx->cacheManager->set($cache_name, $output, 0, $cache_options);
}

return $output;
