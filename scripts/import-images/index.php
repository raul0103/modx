<?php

/**
 * Импорт изображений в miniShop2 из CSV (с поддержкой нескольких изображений)
 */

header('Content-Type: text/html; charset=utf-8');
ini_set("max_execution_time", 0);

define('MODX_API_MODE', true);

$parent_dir = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR;
$index_php = $parent_dir . 'index.php';

$i = 0;
while (!file_exists($index_php) && $i < 9) {
    $parent_dir = dirname(dirname($index_php)) . '/';
    $index_php = $parent_dir . 'index.php';
    $i++;
}

if (file_exists($index_php)) {
    require_once $index_php;
}

if (!is_object($modx)) {
    exit('Ошибка при попытке подгрузить MODX');
}

if (!$minishop2 = $modx->getService('minishop2')) return;

// Функция для загрузки изображения
function download_image($url, $save_path)
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

// Открытие CSV
$csvFile = MODX_BASE_PATH . '/_import-images/test.csv';
$handle = fopen($csvFile, 'r');
if ($handle === false) {
    die("Ошибка открытия файла $csvFile");
}

$images = [];
$parent_ids = [];

$header = fgetcsv($handle, 1000, ';'); // первая строка - заголовок

while (($data = fgetcsv($handle, 1000, ';')) !== false) {
    $alias = $data[0];
    $parent = $data[1];
    $parent_ids[$parent] = true;

    $key = $alias . $parent;
    $images[$key] = [];

    for ($i = 2; $i < count($data); $i++) {
        $url = trim($data[$i]);
        if (!empty($url)) {
            $images[$key][] = $url;
        }
    }
}
fclose($handle);

$parent_ids = array_keys($parent_ids);
$temp_images_dir = MODX_BASE_PATH . '/_import-images/temp_images/';
$log_file = MODX_BASE_PATH . '/_import-images/images.log';

$products = $modx->getIterator('modResource', [
    'parent:IN' => $parent_ids
]);

foreach ($products as $product) {
    $key = $product->alias . $product->parent;

    if (isset($images[$key])) {
        foreach ($images[$key] as $image_url) {
            $path_parts = parse_url($image_url, PHP_URL_PATH);
            $file_name = basename($path_parts);
            $save_path = $temp_images_dir . $product->id . '/' . $file_name;

            if (download_image($image_url, $save_path)) {
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
    }
}
