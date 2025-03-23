<?php

/**
 * Сниппет по $product_id отдат кол-во товара в корзине
 */

if (empty($product_id)) return 0;

if (!class_exists("Main")) {
    require_once MODX_BASE_PATH . $modx->getOption("cart_module_path") . "backend/classes/main.class.php";
}

$main_cart_class = new Main();

$product_count = $main_cart_class->getProductData($product_id);

return $product_count;
