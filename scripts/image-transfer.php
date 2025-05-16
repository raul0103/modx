<?php

/**
 * Переместить ТВ картинки и записать новое значение
 */
$CONFIG = [
    'is_dev' => false, // Выведет в консоль старые и новые пути. Не запустит создание файлов

    'context_key' => 'tagnerud',
    'tv_name' => 'mainImage', // Название TV поля с изображением

    'images_get_path' => "https://gazosilikatstroy.ru", // От куда тащим файлы

    'log' => [
        'passed' => MODX_BASE_PATH . "image-transfer-passed.log", // Пройденные ID
        'report' => MODX_BASE_PATH . "image-transfer-report.log" // Отчет о выполнении
    ],
];

$CONFIG['images_save_path'] = MODX_BASE_PATH . 'assets/images/categories/' . $CONFIG['context_key'] . '/';

// >>> create save dir
if (!is_dir($CONFIG['images_save_path'])) {
    mkdir($CONFIG['images_save_path'], 0777, true);
}
// <<<

// >>> passed - Если скрипт упадает по таймауту, запускаем снова и он учтет уже пройденные ресы
if (file_exists($CONFIG['log']['passed'])) {
    $passed_ids = file_get_contents($CONFIG['log']['passed']);
    $passed_ids = explode(',', $passed_ids);
} else {
    $passed_ids = [0]; // С пустым массиво where не найдет ресурсы
}
// <<<

$resrs = $modx->getIterator('modResource', [
    'class_key' => 'msCategory',
    'context_key' => $CONFIG['context_key'],
    'id:NOT IN' => $passed_ids
]);

function report($report_file_path, $status, $res_id, $tv_image_path, $new_path, $message = "")
{
    file_put_contents($report_file_path, "$status " . json_encode([
        'resource_id' => $res_id,
        'tv_image_path' => $tv_image_path,
        'new_path' => $new_path,
        'error' => $message
    ], JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND | LOCK_EX);
}

foreach ($resrs as $res) {
    $tv_image = $res->getTVValue($CONFIG['tv_name']);
    if (empty($tv_image)) continue;

    $tv_image = ltrim(str_replace('/assets', 'assets', $tv_image), '/'); // нормализация
    $tv_image_path = $CONFIG['images_get_path'] . '/' . $tv_image;
    $local_path = MODX_BASE_PATH . $tv_image;
    $new_path = $CONFIG['images_save_path'] . basename($tv_image);

    if (file_exists($local_path)) {
        report($CONFIG['log']['report'], "ОШИБКА", $res->id, $tv_image_path, $new_path, "Файл уже добавлен");
        continue;
    }

    if ($CONFIG['is_dev']) {
        echo $tv_image_path . ' -> ' . $new_path . PHP_EOL;
    } else {
        $image_data = @file_get_contents($tv_image_path);
        if ($image_data === false) {
            report($CONFIG['log']['report'], "ОШИБКА", $res->id, $tv_image_path, $new_path, "Не удалось скачать изображение");
        } else {
            file_put_contents($new_path, $image_data);
            file_put_contents($CONFIG['log']['passed'], $res->id . PHP_EOL, FILE_APPEND);
            report($CONFIG['log']['report'], "УСПЕХ", $res->id, $tv_image_path, $new_path);

            $res->setTVValue($CONFIG['tv_name'], 'assets/images/categories/' . $CONFIG['context_key'] . '/' . basename($new_path));
            $res->save();
        }
    }
}
