<?php

/**
 * Сниппет отдает массив товаров с большинством полей
 */

if (!class_exists("Main")) {
    require_once MODX_BASE_PATH . $modx->getOption("cart_module_path") . "/backend/classes/main.class.php";
}

$main_cart_class = new Main();

$products = $main_cart_class->getProducts($options);

return $products;
