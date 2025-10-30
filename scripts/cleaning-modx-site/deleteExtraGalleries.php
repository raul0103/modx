<?php

/**
 * Скрипт удаляет с сервера неиспользуемые галереи товаров
 */

// 1. Получаем все галереи на сервере 
$gallery_path = MODX_BASE_PATH . 'assets/images/products';
$gallery_folders = scandir($gallery_path);


$gallery_folders_by_id = []; // Для удобной дальнейшей чистки массива через unset - название папки будет ключом
foreach ($gallery_folders as $gallery_folder) {
    if ($gallery_folder === '.' || $gallery_folder === '..') continue;

    $gallery_folders_by_id[$gallery_folder] = 1;
}

// 2. Получаем id всех товаров на сайте
$sql = "SELECT id FROM modx_site_content WHERE class_key = :class_key";
$stmt = $modx->prepare($sql);
$stmt->execute(['class_key' => 'msProduct']);

// 3. Очищаем массив всех галерей. Оставляем только те товаров для которых уже не существует
while ($row = $stmt->fetch()) {
    $product_id = $row['id'];
    unset($gallery_folders_by_id[$product_id]);
}

// 4. Удаляем с сервера не используемые галереи
function delTree($dir)
{
    if (!file_exists($dir)) return;

    $files = array_diff(scandir($dir), ['..', '.']);

    foreach ($files as $file) {
        $fullpath = $dir . DIRECTORY_SEPARATOR . $file;

        if (is_dir($fullpath)) {
            delTree($fullpath);
        } else {
            unlink($fullpath);
        }
    }

    return rmdir($dir);
}

foreach ($gallery_folders_by_id as $gallery_folder => $value) {
    $fullpath = $gallery_path . '/' . $gallery_folder;

    if (delTree($fullpath)) {
        echo "Удалена $fullpath" . PHP_EOL;
    } else {
        echo "Ошибка $fullpath" . PHP_EOL;
    };
}
