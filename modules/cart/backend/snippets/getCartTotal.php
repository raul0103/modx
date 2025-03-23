<?php

/**
 * Сниппет отдает общее кол-во товаров в корзине
 */

if (!class_exists("Main")) {
    require_once MODX_BASE_PATH . $modx->getOption("cart_module_path") . "/backend/classes/main.class.php";
}

$main_cart_class = new Main();

$cart_total = $main_cart_class->getCartTotal();

return $cart_total;
