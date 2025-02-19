<?php

if (!$pdoTools)
    $pdoTools = $modx->getService("pdoTools");

try {
    $product_total = $pdoTools->runSnippet("@FILE modules/cart/backend/snippets/getCartTotal.php");
} catch (Exception $e) {
}


$data = [
    'cart-product-count' =>   [
        'value' => $product_total['count'] ?: 0,
        'show_error_message' => false
    ],
    'cart-product-summ' =>   [
        'value' => $product_total['summ'] ?: 0,
        'show_error_message' => false
    ],
    'cart-product-summ-old' =>   [
        'value' => $product_total['old_summ'] ?: 0,
        'show_error_message' => false
    ],
];

return [
    'config_prefix' => "counters",
    'placeholders' => $data,
];
