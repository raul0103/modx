<?php

/**
 * Переместить ТВ картинки и записать новое значение
 */

$ctx = 'krovelnyjstroymarket';
$tv_image_name = 'mainImage';
$images_save_path = MODX_BASE_PATH . 'assets/images/categories/';

// >>> create save dir
if (!is_dir($images_save_path)) {
    mkdir($images_save_path, 0777, true);
}
// <<<

// >>> logs
$filelog = MODX_BASE_PATH . "image-transfer-log.log";
if (file_exists($filelog)) {
    $passed_ids = file_get_contents($filelog);
    $passed_ids = explode(',', $passed_ids);
} else {
    $passed_ids = [0]; // С пустым массиво where не найдет ресурсы
}
// <<<

$resrs = $modx->getIterator('modResource', [
    // 'id' => 230740,
    // 'class_key' => 'msProduct',
    'context_key' => $ctx,
    'id:NOT IN' => $passed_ids
]);

foreach ($resrs as $res) {


    $tv_image = $res->getTVValue($tv_image_name);
    if (empty($tv_image) || strpos($tv_image, '_import') == false) continue;

    $tv_image_path = MODX_BASE_PATH . $tv_image;
    $tv_image_path = str_replace('/assets/_import', '_import', $tv_image_path);
    $filename = basename($tv_image);
    // echo $tv_image_path . ' -> ' . $images_save_path . $filename;

    if (file_exists($tv_image_path)) file_put_contents($filelog, $res->id . ',', FILE_APPEND);

    // if (copy($tv_image_path, $images_save_path . $filename)) {
    //     // echo "Картинка успешно скопирована в новую папку!";

    //     echo "images/categories/" . $filename;
    // }
}


// /home/stroymarket/web/alterteplo.ru/public_html/_import/files/import_images/774q3w13a66jo8mhvohlo26vo8jiir9m2 (1).jpg -> /home/stroymarket/web/alterteplo.ru/public_html/assets/images/categories/774q3w13a66jo8mhvohlo26vo8jiir9m2 (1).jpg


// /home/stroymarket/web/alterteplo.ru/public_html/_import/files/import_images/774q3w13a66jo8mhvohlo26vo8jiir9m2 (1).jpg

// /home/stroymarket/web/alterteplo.ru/public_html/_import/files/import_images/3123123123.jpg