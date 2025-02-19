<?php

$cache_name = $modx->resource->context_key;
$cache_options = [
    xPDO::OPT_CACHE_KEY => 'default/config-placeholder/',
];

if (!$result = $modx->cacheManager->get($cache_name, $cache_options)) {

    // Путь к папке
    $directory = MODX_CORE_PATH . "/elements/config/placeholders/data";
    if (!is_dir($directory)) return;

    // Получаем список файлов из папки
    $files = scandir($directory);

    // Перебираем каждый файл
    $result = [
        'main' => [],
        'errors' => []
    ];
    foreach ($files as $file) {
        // Пропускаем текущую и родительскую директории
        if ($file === '.' || $file === '..') {
            continue;
        }

        // Путь к файлу
        $file_path = $directory . '/' . $file;

        // Проверяем, что это файл и его расширение .php
        if (is_file($file_path) && pathinfo($file_path, PATHINFO_EXTENSION) === 'php') {
            $data = include $file_path; // Подключаем файл
            $prefix = $data['config_prefix'];

            foreach ($data['placeholders'] as $placeholder_key => $placeholder_data) {
                $key = $placeholder_key;
                $value = $placeholder_data['value'];

                // Если значения нет, и имеется флаг show_error_message, выводим ошибку в значение плейсхолдера
                if (empty($value) && $placeholder_data['show_error_message']) {
                    $result['main']["$prefix.$key"] = "Не найден $prefix.$key";
                } else {
                    $result['main']["$prefix.$key"] = $value;
                }

                // Все пустые значения. Выводятся в layouts/base.tpl для авторизованного админа
                if (empty($value) && $value != 0) {
                    $result['errors'][] = "$prefix.$key";
                }
            }
        }
    }

    $modx->cacheManager->set($cache_name, $result, 0, $cache_options);
}

foreach ($result['main'] as $key => $value) {
    $modx->setPlaceholder($key, $value);
}

$modx->setPlaceholder("config.errors",  $result['errors']);
