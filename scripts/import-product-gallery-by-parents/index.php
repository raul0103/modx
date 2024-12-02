<?php


// define('MODX_API_MODE', true);
// include 'index.php';


$file = MODX_BASE_PATH . 'data.csv';
$separator = ',';
$images_path = MODX_BASE_PATH . "_import/files/import_images/";

$minishop = $modx->getService('miniShop2');

if (!file_exists($file)) die("Файл не найден: $file");

$contents = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

$data = [];
foreach ($contents as $line) {
    $line_data = explode($separator, $line);
    $parent_id = array_shift($line_data);
    $products = $modx->getCollection('modResource', [
        'parent' => $parent_id,
        'class_key' => 'msProduct'
    ]);

    foreach ($products as $product) {
        foreach ($line_data as $image) {
            if (!file_exists($images_path . $image)) continue;

            $minishop->runProcessor('mgr/gallery/upload', [
                'id' => $product->id,
                'file' => $images_path . $image
            ]);
        }
    }

    echo "parent: $parent_id<br>";
}
