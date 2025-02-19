<?php

/**
 * Ссылки на ресурсы генерируемые через makeURL
 */

$data = [
    'cart' => [
        'value' => $modx->makeURL($modx->getOption('cart_id')), // ID ресурса корзины
        'show_error_message' => false
    ],
    'favorites' => [
        'value' =>  $modx->makeURL($modx->getOption('favorites_id')), // ID ресурса избранных товаров
        'show_error_message' => false
    ],
    'comparison' => [
        'value' =>  $modx->makeURL($modx->getOption('comparison_id')), // ID ресурса сравнения товаров
        'show_error_message' => false
    ],
    'catalog' => [
        'value' => $modx->makeURL($modx->getOption('catalog_id')), // ID ресурса каталог
        'show_error_message' => false
    ],
    'policy' => [
        'value' => $modx->makeURL($modx->getOption('policy_id')), // ID ресурса политики конфиденциальности
        'show_error_message' => false
    ],
    'garantii' => [
        'value' => $modx->makeURL($modx->getOption('garantii_id')), // ID ресурса гарантии
        'show_error_message' => false
    ],
    'dostavka' => [
        'value' => $modx->makeURL($modx->getOption('dostavka_id')), // ID ресурса доставка
        'show_error_message' => false
    ],
];

return [
    'config_prefix' => "makeurls",
    'placeholders' => $data,
];
