<?php

/**
 * Безопасный импорт изображений в miniShop2 из CSV
 * Оптимизирован по памяти
 */

ini_set('max_execution_time', 0);
ini_set('memory_limit', '1G');

$file_name = '0.csv';

if (!$minishop2 = $modx->getService('minishop2')) {
    die('miniShop2 not loaded');
}

$baseDir = MODX_BASE_PATH . '/_import-images/';
$csvFile = $baseDir . $file_name;
$logFile = $baseDir . pathinfo($file_name, PATHINFO_FILENAME) . '.log';
$tempDir = $baseDir . 'temp_images/';

/* =========================
   Вспомогательные функции
   ========================= */

/**
 * Проверка — есть ли уже такая картинка у товара
 */
function productHasImage($modx, $productId, $fileName)
{
    return (bool)$modx->getObject('msProductFile', [
        'product_id' => $productId,
        'file'       => $fileName
    ]);
}

/**
 * Потоковая загрузка файла (минимум памяти)
 */
function downloadImageStream($url, $savePath)
{
    $dir = dirname($savePath);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $fp = fopen($savePath, 'w');
    if (!$fp) return false;

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_FILE           => $fp,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT        => 60,
        CURLOPT_FAILONERROR    => true,
        CURLOPT_USERAGENT      => 'MODX image importer'
    ]);

    $result = curl_exec($ch);
    curl_close($ch);
    fclose($fp);

    if (!$result) {
        @unlink($savePath);
        return false;
    }

    return true;
}

/**
 * Основной обработчик картинки
 */
function handleImage($modx, $minishop2, $product, $imageUrl, $tempDir, $logFile)
{
    $path = parse_url($imageUrl, PHP_URL_PATH);
    $fileName = basename($path);

    if (!$fileName) return;

    // Проверяем, есть ли уже такая картинка
    if (productHasImage($modx, $product->id, $fileName)) {
        $msg = "{$product->alias}|{$product->parent}|SKIP: exists\n";
        file_put_contents($logFile, $msg, FILE_APPEND);
        return;
    }

    $tempPath = $tempDir . $product->id . '/' . $fileName;

    if (!downloadImageStream($imageUrl, $tempPath)) {
        $msg = "{$product->alias}|{$product->parent}|ERROR: download\n";
        file_put_contents($logFile, $msg, FILE_APPEND);
        return;
    }

    $res = $minishop2->runProcessor('mgr/gallery/upload', [
        'id'   => $product->id,
        'file' => $tempPath
    ]);

    if ($res->isError()) {
        $msg = "{$product->alias}|{$product->parent}|ERROR: upload\n";
    } else {
        $msg = "{$product->alias}|{$product->parent}|OK\n";
    }

    file_put_contents($logFile, $msg, FILE_APPEND);

    // Удаляем temp-файл
    @unlink($tempPath);
}

/* =========================
   Работа с CSV
   ========================= */

if (!file_exists($csvFile)) {
    die("CSV not found");
}

$processed = [];
if (file_exists($logFile)) {
    foreach (file($logFile, FILE_IGNORE_NEW_LINES) as $line) {
        [$a, $p] = explode('|', $line);
        $processed[$a . '|' . $p] = true;
    }
}

$handle = fopen($csvFile, 'r');
if (!$handle) die('CSV open error');

$header = fgetcsv($handle, 0, ';');

while (($data = fgetcsv($handle, 0, ';')) !== false) {

    $product_alias = trim($data[0]);
    $parent_alias  = trim($data[1]);

    if (isset($processed[$product_alias . '|' . $parent_alias])) {
        continue;
    }

    $parent = $modx->getObject('modResource', ['alias' => $parent_alias]);
    if (!$parent) continue;

    $product = $modx->getObject('modResource', [
        'parent' => $parent->id,
        'alias'  => $product_alias
    ]);
    if (!$product) continue;

    for ($i = 2; $i < count($data); $i++) {
        $url = trim($data[$i]);
        if ($url) {
            handleImage($modx, $minishop2, $product, $url, $tempDir, $logFile);
        }
    }

    // освобождаем память
    unset($product, $parent);
}

fclose($handle);

echo "Import finished\n";
