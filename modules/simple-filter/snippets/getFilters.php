<?php

/**
 * по $parents формирует массив фильтров по опциям товаров
 * 1. Получаем ID дочерних ресурсов
 * 2. По полученным IDs товаров собираем опции
 * 
 * $parents
 * $filter_uniqueid // Важная переменная определяющая блок с фильтрами. Благодаря ей могут быть вызваны несколько фильтров на странцие
 */
if (!isset($parents)) return false;

define('FILTER_UNIQUEID', $filter_uniqueid ?: "filter");
define('DEPTH_CHILDS', $depth ?: 10); // Глубина поиска дочерних ресурсов
define('WHERE_CHILDS',  ['hidemenu' => 0, 'published' => 1, 'context_key' => $modx->context->key]);


$module_path = MODX_CORE_PATH . 'elements/modules/simple-filter/';
require "$module_path/snippets/utils/sfCache.php";
require "$module_path/snippets/utils/sfWhereToString.php";

if (!isset($sfCache)) {
    $sfCache = new sfCache();
}
if (!$filters = $sfCache->get("getFilters", md5(serialize($scriptProperties)))) {
    /**
     * 1. Получаем ID дочерних ресурсов 
     */
    $where_string = sfWhereToString(WHERE_CHILDS);

    $parent_ids = []; // Родители после каждой итерации, для получения следующей вложенности
    $product_ids = [];
    for ($i = 0; $i <= DEPTH_CHILDS; $i++) {
        if ($i > 0 && empty($parent_ids)) continue;

        if ($i > 0) {
            $parents = implode(',', $parent_ids);
        }

        $sql = "SELECT id,class_key FROM {$modx->getOption('table_prefix')}site_content WHERE $where_string AND parent IN ($parents)";
        $result = $modx->query($sql);


        $ids = [
            'msProduct' => [],
            'msCategory' => []
        ];
        if ($result) {
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                if (isset($ids[$row['class_key']])) {
                    $ids[$row['class_key']][] = $row['id'];
                }
            }
        }

        $parent_ids = $ids['msCategory'];
        $product_ids = array_merge($product_ids, $ids['msProduct']);
    }

    /**
     * 2. По полученным IDs товаров собираем опции
     */
    if (empty($product_ids)) return null;

    $product_ids = implode(',', $product_ids);
    if (isset($options)) {
        $where_options = " AND o.`key` IN ('" . implode("','", $options) . "')";
    }
    $sql = "SELECT 
                po.`key` AS `key`, 
                o.`caption`,
                GROUP_CONCAT(DISTINCT po.`value` ORDER BY po.`value` ASC) AS `values`
            FROM 
                {$modx->getOption('table_prefix')}ms2_product_options AS po
            LEFT JOIN 
                {$modx->getOption('table_prefix')}ms2_options AS o 
                ON o.`key` = po.`key`
            WHERE 
                po.product_id IN ($product_ids)
                $where_options
            GROUP BY 
                po.`key`, o.`caption`;";

    $result = $modx->query($sql);
    $filters = $result->fetchAll(PDO::FETCH_ASSOC);

    // Значения фильтров в массив
    foreach ($filters as &$filter) {
        $filter['values'] = explode(',', $filter['values']);
    }

    $sfCache->set("getFilters", md5(serialize($scriptProperties)), $filters);
}

$pdoTools = $modx->getService('pdoTools');
return $pdoTools->getChunk($tpl, [
    'filters' => $filters,
    'filter_uniqueid' => $filter_uniqueid,
]);
