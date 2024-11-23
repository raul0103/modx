<?php

/**
 * 1. Получаем категории по глубине DEPTH
 * 2. Получаем по ID категорий товары с пагинацией
 * 
 * $tpl
 * $limit
 * $parents
 * $options // Какие опции получить по товарам для вывода
 * $filter_uniqueid // Важная переменная определяющая блок с фильтрами. Благодаря ей могут быть вызваны несколько фильтров на странцие
 */
if (!isset($parents)) return false;
if (!isset($filter_uniqueid)) $filter_uniqueid = "filter";

define('FILTER_UNIQUEID', $filter_uniqueid ?: "filter");
define('DEPTH_CATEGORIES', $depth ?: 10);
define('WHERE_CATEGORIES',  ['hidemenu' => 0, 'published' => 1, 'class_key' => 'msCategory', 'context_key' => $modx->context->key]);
define('WHERE_PRODUCTS',  ['hidemenu' => 0, 'published' => 1, 'class_key' => 'msProduct', 'context_key' => $modx->context->key]);
define('LIMIT_PRODUCTS', $limit ?: 10);

$module_path = MODX_CORE_PATH . 'elements/modules/simple-filter/';
require "$module_path/snippets/utils/sfCache.php";
require "$module_path/snippets/utils/sfWhereToString.php";
require "$module_path/snippets/utils/sfProductOptions.php";
require "$module_path/snippets/utils/sfWhereFilters.php";

$tp = $modx->getOption('table_prefix');

/**
 * 1. Получаем категории по глубине DEPTH_CATEGORIES
 */

$where_string = sfWhereToString(WHERE_CATEGORIES);
$parent_ids = []; // Родители после каждой итерации, для получения следующей вложенности
$category_ids = explode(',', $parents);
for ($i = 0; $i <= DEPTH_CATEGORIES; $i++) {
    if ($i > 0 && empty($parent_ids)) continue;

    if ($i > 0) {
        $parents = implode(',', $parent_ids);
    }

    $sql = "SELECT id FROM {$tp}site_content WHERE $where_string AND parent IN ($parents)";
    $result = $modx->query($sql);
    $rows = $result->fetchAll(PDO::FETCH_COLUMN, 0);

    $parent_ids = $rows;

    $category_ids = array_merge($category_ids, $rows);
}

/**
 * 2. Получаем по ID категорий товары с пагинацией
 */
if (empty($category_ids)) return null;

$category_ids = implode(',', $category_ids);
$where_string = sfWhereToString(WHERE_PRODUCTS);

// Опции товара. Получаем для вывода их на странице в карточке товара
$product_opions = sfProductOptions($options, 'po');
// Фильтр. Формируется если есть get параметры
$where_filters = sfWhereFilters($get_params, FILTER_UNIQUEID, 'po');

// >>> Получение товаров
$params = $get_params ?: $_GET;
if ($params[$filter_uniqueid]) {
    $data = json_decode(urldecode($params[$filter_uniqueid]), true);
    $page = $data['page'] ?: 1;
} else {
    $page = 1;
}
$offset = ($page - 1) * LIMIT_PRODUCTS;

$sql = "SELECT sc.id,sc.pagetitle,sc.uri,msp.*
            $product_opions -- Какие опции получить по товарам для вывода
        FROM
            {$tp}site_content AS sc
        LEFT JOIN 
            {$tp}ms2_products AS msp 
            ON msp.id = sc.id
        LEFT JOIN 
            {$tp}ms2_product_options AS po
            ON po.product_id = sc.id
        WHERE
            $where_string
            AND sc.parent IN ($category_ids)
            $where_filters -- Условие по фильтрам
        GROUP BY
            sc.id
        LIMIT " . LIMIT_PRODUCTS . " OFFSET $offset;";
$result = $modx->query($sql);
$products = $result->fetchAll(PDO::FETCH_ASSOC);
// <<<

// >>> Пагинация
$sql = "SELECT COUNT(DISTINCT sc.id) AS total
        FROM
            {$tp}site_content AS sc
        LEFT JOIN 
            {$tp}ms2_product_options AS po
            ON po.product_id = sc.id
        WHERE
            $where_string
        AND sc.parent IN ($category_ids)
            $where_filters;";
$total_result = $modx->query($sql);
$total = $total_result->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total / LIMIT_PRODUCTS);
// <<<

// >>> Сохраняю в кэш параметры вызова сниппета. Они будут необходимы при ajax загрузке товаров и повторном вызове сниппета с такими же параметрами
if (!isset($sfCache)) {
    $sfCache = new sfCache();
}
$sfCache->set("scriptProperties", $filter_uniqueid, $scriptProperties);
// <<<

$pdoTools = $modx->getService('pdoTools');
return $pdoTools->getChunk($tpl, [
    'products' => $products,
    'filter_uniqueid' => $filter_uniqueid,
    'options' => $options,
    'total_pages' => $total_pages
]);
