<?php

ini_set("max_execution_time", 0);

/**
 * Скрипт создает CSV файл в корне сайта
 * alias товара + alias родителя + ссылки на картинки
 * 
 * сохарнит результат в _export-product-gallery/offset.csv
 */

$limit = 10;
$offset = 0;
$context_key = "krovelnyjstroymarket";
$domain_images = "gazosilikatstroy.ru"; // Домен от куда получать картинки

// Получение товаров
$query = $modx->newQuery('msProduct');
$query->limit($limit, $offset); // limit, offset
$where = [
    'context_key' => $context_key,
    'class_key' => "msProduct",
];

$query->where($where);
$products = $modx->getCollection('msProduct', $query);

$product_ids = []; // ID всех товаров для получения по ним картинок 

foreach ($products as $product) {
    $product_ids[] = $product->id;
}

// GET IMAGES
$table_prefix = $modx->getOption('table_prefix');
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


$header_keys =  ['product_alias', 'parent_alias'];

// RESULT
$value_rows = "";
foreach ($products as $product) {
    $parent = $modx->getObject('modResource', $product->parent);
    if ($parent->get('context_key') != $context_key) continue;

    $value_rows .= $product->alias . ';';
    $value_rows .= $parent->alias . ';';

    // images
    $images = $id_images[$product->id];
    foreach ($images as $image_idx => $image) {
        $image_key = "image-$image_idx";
        if (!in_array($image_key, $header_keys)) {
            $header_keys[] = $image_key;
        }
        $value_rows .= "https://$domain_images/$image" . ';';
    }

    $value_rows .= PHP_EOL;
}

$header_keys = implode(';', $header_keys) . PHP_EOL;
$output = $header_keys . $value_rows;

// SAVE
$folder = MODX_BASE_PATH . "_export-images";

if (!is_dir($folder)) mkdir($folder, 0777, true);

$filename = "$offset.csv";
$full_path = $folder . '/' . $filename;

file_put_contents($full_path, $output);
