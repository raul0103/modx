<?php

/**
 * Импорт изображений в miniShop2 из CSV
 */

ini_set("max_execution_time", 0);

$file_name = '0.csv';

if (!$minishop2 = $modx->getService('minishop2')) return;

$csvFile = MODX_BASE_PATH . "/_import-images/$file_name";
$logFile = MODX_BASE_PATH . "/_import-images/" . pathinfo($file_name, PATHINFO_FILENAME) . ".log";

// --- Считываем уже обработанные товары из лога ---
$processed = [];
if (file_exists($logFile)) {
    $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Линия: product_alias|parent_alias|status
        [$product_alias, $parent_alias, $status] = explode('|', $line);
        $processed[trim($product_alias) . '|' . trim($parent_alias)] = true;
    }
}

// Функция для загрузки изображения
function downloadImage($url, $save_path)
{
    $dir = dirname($save_path);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $image_data = file_get_contents($url);
    if ($image_data === false) {
        return false;
    }

    file_put_contents($save_path, $image_data);
    return true;
}

// Функция обработки изображения
function handlerImage($image_url, $product, $minishop2, $logFile)
{
    $temp_images_dir = MODX_BASE_PATH . '/_import-images/temp_images/';

    $path_parts = parse_url($image_url, PHP_URL_PATH);
    $file_name = basename($path_parts);
    $save_path = $temp_images_dir . $product->id . '/' . $file_name;

    if (downloadImage($image_url, $save_path)) {
        $res = $minishop2->runProcessor('mgr/gallery/upload', [
            'id' => $product->id,
            'file' => $save_path
        ]);

        if ($res->isError()) {
            $status = "ERROR: " . implode('; ', $res->getAllErrors());
        } else {
            $status = "OK";
        }
    } else {
        $status = "ERROR: download failed";
    }

    // Логируем product_alias | parent_alias | статус
    $message = $product->alias . '|' . $product->parent . '|' . $status . "\n";
    file_put_contents($logFile, $message, FILE_APPEND);

    echo $message;
}

// --- Работа с CSV ---
$handle = fopen($csvFile, 'r');
if ($handle === false) die("Ошибка открытия файла $csvFile");

$header = fgetcsv($handle, 1000, ';'); // первая строка - заголовок

while (($data = fgetcsv($handle, 1000, ';')) !== false) {
    $product_alias = trim($data[0]);
    $parent_alias = trim($data[1]);

    // Проверяем, не обработан ли уже этот товар
    if (isset($processed[$product_alias . '|' . $parent_alias])) continue;

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

        handlerImage($image_url, $resource, $minishop2, $logFile);
    }
}

fclose($handle);
