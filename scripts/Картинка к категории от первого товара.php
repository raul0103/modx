<?php

/**
 * Скрипт генерирует TV поле изображения категории по первому товару данной категории
 * Сейчас копируются только картинки которые отдают на категории 404
 * Пройденные категории кэшируются по ID для повторного запуска. Так как скрипт может упасть при большем кол-ве данных
 */

$options = [];
$options['main'] = [
    'context_key' => 'web', // Укажите здесь контекст
    'cache_file' => MODX_BASE_PATH . 'tmp_cache.log' // Файл для кэширования пройденных категорий
];

// Используем уже определенное значение 'context_key'
$options['category'] = [
    'tv_image_name' => 'mainImage', // Название TV поля картинки категории
    'images_path' => 'assets/images/categories/' . $options['main']['context_key'] . '/' // Путь куда будут скопированы новые картинки
];


// Получаем пройденные ID категорий
$filelog = $options['main']['cache_file'];
if (file_exists($filelog)) {
    $passed_ids = file_get_contents($filelog);
    $passed_ids = explode(',', $passed_ids);
} else {
    $passed_ids = [0]; // С пустым массиво where не найдет ресурсы
}

$categories = $modx->getIterator('modResource', [
    'class_key' => 'msCategory',
    'context_key' => $options['main']['context_key'],
    'id:NOT IN' => $passed_ids
]);

foreach ($categories as $category) {
    file_put_contents($filelog, $category->id . ',', FILE_APPEND);

    $product = $modx->getObject('msProduct', [
        'parent' => $category->id
    ]);
    $category_image = $category->getTVValue($options['category']['tv_image_name']);

    if (!$product || !$product->get('image') || file_exists(MODX_BASE_PATH . $category_image)) continue;

    $product_image = MODX_BASE_PATH . $product->get('image');
    if (file_exists($product_image)) {
        if (!is_dir(MODX_BASE_PATH . $options['category']['images_path'])) {
            mkdir(MODX_BASE_PATH . $options['category']['images_path'], 0755, true);
        }

        $new_image_path = rtrim($options['category']['images_path'], '/') . '/' . basename($product_image);
        copy($product_image,  MODX_BASE_PATH . $new_image_path);

        $category->setTVValue($options['category']['tv_image_name'], $new_image_path);
    }
}
