<?php

/**
 * Скрипт создает CSV файл в корне сайта, с данными по опциям товаров выбранного контекста
 * Об оптимизации не думал, накидал на коленке
 */

$limit = 5000;
$offset = 0;
$context_key = "metallprofil";
$class_key = "msProduct";
$resources = []; // ID ресурсов для выборки, если пусто то не учитывается
$table_prefix = $modx->getOption('table_prefix');

//> Подключаем MODX
@include_once(dirname(dirname(__DIR__)) . '/config.core.php');
@include_once(dirname(dirname(__DIR__)) . '/core/model/modx/modx.class.php');

$modx = new modX();
$modx->initialize('mgr');
//<



/**
 * Сбор товаров и их опций
 */
$query = $modx->newQuery('msProduct');
$query->limit($limit, $offset); // limit, offset
$where = [
    'context_key' => $context_key,
    'class_key' => $class_key,
];
if (!empty($resources)) {
    $where['id:in'] = $resources;
}
$query->where($where);
$products = $modx->getCollection('msProduct', $query);

$product_ids = []; // ID всех товаров для получения по ним картинок 

foreach ($products as $product) {
    $product_ids[] = $product->id;
}

/**
 * Сбор картинок
 */
$images_result = $modx->query("
SELECT
    pf.url,
    sc.id
FROM
    {$table_prefix}ms2_product_files AS pf
LEFT JOIN {$table_prefix}site_content AS sc
ON
    sc.id = pf.product_id
WHERE
    pf.parent = 0 AND sc.id IN (" . implode(',', $product_ids) . ")");
$id_images = [];
while ($r = $images_result->fetch(PDO::FETCH_ASSOC)) {
    $id = $r['id'];
    if (empty($id_images[$id])) $id_images[$id] = [];
    $id_images[$id][] = $r['url'];
}

/**
 * Формируем заголовки
 */
$header_keys =  ['alias', 'parent'];

/**
 * Формируем результаты
 */
$value_rows = "";
foreach ($products as $product) {
    $value_rows .= $product->alias . ';';
    $value_rows .= $product->parent . ';';

    // Картинки
    $images = $id_images[$product->id];
    foreach ($images as $image_idx => $image) {
        $image_key = "image-$image_idx";
        if (!in_array($image_key, $header_keys)) {
            $header_keys[] = $image_key;
        }
        $value_rows .= "https://www-knauf.ru/$image" . ';';
    }

    $value_rows .= PHP_EOL;
}

$header_keys = implode(';', $header_keys) . PHP_EOL;
$output = $header_keys . $value_rows;

/**
 * Сохраняем результат в файл
 */
$filename = "IMAGES.csv";
$full_path = MODX_BASE_PATH . $filename;
file_put_contents($full_path, $output);
