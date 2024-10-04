<?php

/**
 * Через консоль ОЧЕНЬ быстро рабоает
 */

// Устанавливаем кодировку и время выполнения
header('Content-Type: text/html; charset=utf-8');
ini_set("max_execution_time", 0);

// Подключаем MODX
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
    // Создаем директории, если их нет
    $dir = dirname($save_path);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true); // Рекурсивно создаем директории
    }

    // Загружаем файл
    $image_data = file_get_contents($url);
    if ($image_data === false) {
        echo "Ошибка загрузки: $url\n";
        return false;
    }

    // Сохраняем файл
    file_put_contents($save_path, $image_data);
    return true;
}

// Открываем CSV файл
$csvFile = MODX_BASE_PATH . '/_import-images/main.csv'; // Укажите путь к вашему CSV файлу
$handle = fopen($csvFile, 'r');
if ($handle === false) {
    die("Ошибка открытия файла $csvFile");
}

$images = [];

$parent_ids = [];

// Читаем CSV файл построчно
while (($data = fgetcsv($handle, 1000, ',')) !== false) {
    if (count($data) < 3) continue; // Пропускаем строки с недостаточным количеством данных

    // Структура CSV: alias,parent,image-1
    list($alias, $parent, $image_url) = $data;

    // Создаем ключ alias+parent
    $key = $alias . $parent;

    $parent_ids[$parent] = $parent;

    // Добавляем в массив
    $images[$key] = $image_url;
}
fclose($handle);

$parent_ids = array_keys($parent_ids);


// Путь к директории для сохранения изображений
$temp_images_dir = MODX_BASE_PATH . '/_import-images/temp_images/';

// Пример массива товаров
$products = $modx->getIterator('modResource', [
    'parent:in' => $parent_ids
]);

$log_file = MODX_BASE_PATH . '/_import-images/images.log';

foreach ($products as $product) {
    // Генерируем ключ для товара
    $key = $product->alias . $product->parent;

    if (isset($images[$key])) {
        // Получаем URL изображения
        $image_url = $images[$key];

        // Формируем путь для сохранения изображения
        $image_path_parts = parse_url($image_url, PHP_URL_PATH);
        $save_path = $temp_images_dir . $image_path_parts;

        // Загружаем и сохраняем изображение
        if (download_image($image_url, $save_path)) {
            $message = $product->id . ' | ' . $product->alias . ' | ' . $product->parent . "\n";

            echo $message;

            file_put_contents($log_file, $message, FILE_APPEND);

            $res = $minishop2->runProcessor('mgr/gallery/upload', [
                'id' => $product->id,
                'file' => $save_path
            ]);
        } else {
            $message = "Не удалось сохранить изображение: $image_url\n";
            file_put_contents($log_file, $message, FILE_APPEND);
        }
    }
}
