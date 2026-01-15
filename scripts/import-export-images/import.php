<?php

/**
 * Импорт изображений в miniShop2 из CSV
 */

ini_set("max_execution_time", 0);

$file_name = '0.csv';

if (!$minishop2 = $modx->getService('minishop2')) return;

// Функция для загрузки изображения
function downloadImage($url, $save_path)
{
    $dir = dirname($save_path);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $image_data = file_get_contents($url);
    if ($image_data === false) {
        echo "Ошибка загрузки: $url\n";
        return false;
    }

    file_put_contents($save_path, $image_data);
    return true;
}

function handlerImage($image_url, $product, $minishop2)
{
    $temp_images_dir = MODX_BASE_PATH . '/_import-images/temp_images/';
    $log_file = MODX_BASE_PATH . '/_import-images/log.log';

    $path_parts = parse_url($image_url, PHP_URL_PATH);
    $file_name = basename($path_parts);
    $save_path = $temp_images_dir . $product->id . '/' . $file_name;

    if (downloadImage($image_url, $save_path)) {
        $message = $product->id . ' | ' . $product->alias . ' | ' . $product->parent . ' | ' . $file_name . "\n";
        echo $message;
        file_put_contents($log_file, $message, FILE_APPEND);

        $res = $minishop2->runProcessor('mgr/gallery/upload', [
            'id' => $product->id,
            'file' => $save_path
        ]);

        if ($res->isError()) {
            file_put_contents($log_file, "Ошибка загрузки в miniShop2: " . print_r($res->getAllErrors(), true) . "\n", FILE_APPEND);
        }
    } else {
        $message = "❌ Не удалось сохранить изображение: $image_url\n";
        echo $message;
        file_put_contents($log_file, $message, FILE_APPEND);
    }
}

// Открытие CSV
$csvFile = MODX_BASE_PATH . "/_import-images/$file_name";
$handle = fopen($csvFile, 'r');
if ($handle === false) die("Ошибка открытия файла $csvFile");

$images = [];
$parent_ids = [];

$header = fgetcsv($handle, 1000, ';'); // первая строка - заголовок

while (($data = fgetcsv($handle, 1000, ';')) !== false) {
    $product_alias = $data[0];
    $parent_alias = $data[1];

    $parent = $modx->getObject('modResource', ['alias' => $parent_alias]);
    if (!$parent) continue;

    $resource = $modx->getObject('modResource', [
        'parent' => $parent->id,
        'alias' => $product_alias,
    ]);
    if (!$resource) continue;

    // Первые 2 индекса это product_alias и parent_alias
    for ($i = 2; $i < count($data); $i++) {
        $image_url = trim($data[$i]);
        if (empty($image_url)) continue;

        handlerImage($image_url, $resource, $minishop2);
    }
}

fclose($handle);
