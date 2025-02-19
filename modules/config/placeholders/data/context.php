<?php

/**
 * Настройки контекста не имеющие категории
 */

$data = [
    'descriptor' => [
        'value' => $modx->getOption('descriptor'),
        'show_error_message' => true
    ],
    'catalog_id' => [
        'value' => $modx->getOption('catalog_id'), // ID Каталога
        'show_error_message' => false
    ],
    'header_menu_ids' => [
        'value' => $modx->getOption('header_menu_ids'), // ID элементов меню в header
        'show_error_message' => false
    ],
    'header_mobile_categories_ids' => [
        'value' => $modx->getOption('header_mobile_categories_ids'),  // [MOBILE] ID категорий отображаемые в header
        'show_error_message' => false
    ],
    'footer_menu_ids' => [
        'value' => $modx->getOption('footer_menu_ids'), // ID элементов меню в footer
        'show_error_message' => false
    ],
    'footer_category_ids' => [
        'value' => $modx->getOption('footer_category_ids'), // ID категорий отображаемые в footer
        'show_error_message' => false
    ],
];

return [
    'config_prefix' => "context",
    'placeholders' => $data,
];
