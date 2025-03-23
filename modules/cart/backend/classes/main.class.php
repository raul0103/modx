<?php

require_once  "session.class.php";
require_once  "helpers.class.php";

/**
 * Класс с основными методами используемыми во всех процессорах
 */
class Main
{
    public $session;
    public $helpers;
    public function __construct()
    {
        $this->session = new Session();
        $this->helpers = new Helpers();
    }
    /**
     * Возвращает кол-во товара в корзине по product_id
     * @param mixed $product_id
     * @return mixed
     */
    public function getProductData($product_id)
    {
        $cart_items = $this->session->get();

        if (empty($cart_items)) return 0;

        $product_count = 0;
        foreach ($cart_items as $cart_item) {
            if ($cart_item['id'] == $product_id) {
                $product_count = $cart_item['count'];
                break;
            }
        }

        // $summ = (int)$product_count['count'] * $this->helpers->parseNumber($product_count['price']);

        return [
            "count" => $product_count,
            // "summ" => $summ
        ];
    }

    /**
     * возвращает общее число товаров в корзине
     * @param mixed $cart_items - опционально
     * @return array|int
     */
    public function getCartTotal($cart_items = null)
    {

        if (!$cart_items) {
            $cart_items = $this->session->get();
        }

        if (empty($cart_items)) return 0;

        $total_summ = [
            'summ' => 0,
            'old' => 0
        ];
        foreach ($cart_items as $cart_item) {
            $total_summ['summ'] += $this->calcSumm($cart_item['count'], $cart_item['price'], false);
            $total_summ['old_summ'] += $this->calcSumm($cart_item['count'], $cart_item['old_price'], false);
        }

        return [
            "count" => count($cart_items),
            "summ" => $this->helpers->formattedNumber($total_summ['summ']),
            "old_summ" => $this->helpers->formattedNumber($total_summ['old_summ']),
        ];
    }

    /**
     * Отдает массив товаров в корзине
     * @return array $products
     */
    public function getProducts()
    {
        global $modx;

        $cart_items = $this->session->get();

        if (empty($cart_items)) return [];

        $cart_items_by_id = [];
        foreach ($cart_items as $cart_item) {
            $cart_items_by_id[$cart_item['id']] = $cart_item;
        }

        // Товары с базы для получения необходимых полей
        $ms_products = $modx->getIterator('msProduct', [
            'id:in' => array_keys($cart_items_by_id)
        ]);

        $output = [];

        foreach ($ms_products as $ms_product) {
            $product_id = $ms_product->get("id");

            // Товар из корзины для получения необходимых полей 
            $cart_item = $cart_items_by_id[$product_id];

            $summ = $this->calcSumm($cart_item['count'], $cart_item['price']);

            $output[] = [
                "id" => $product_id,
                "pagetitle" => $ms_product->get("pagetitle"),
                "menutitle" => $ms_product->get("menutitle"),
                "uri" => $ms_product->get("uri"),
                "thumb" =>  $ms_product->get("thumb"),
                "unit" => $cart_item['unit'] ?: $ms_product->get("unit")[0], // Получаем из корзины, вдруг была подмена 
                "price" => $cart_item['price'] ?: $ms_product->get("price"), // Получаем из корзины, вдруг была подмена 
                "old_price" => $cart_item['old_price'] ?: $ms_product->get("old_price"),
                "count" =>  $cart_item['count'],
                "summ" => $summ
            ];
        }

        return $output;
    }


    public function calcSumm($count, $price, $formatted = true)
    {
        $summ = (int)$count * $this->helpers->parseNumber($price);

        if ($formatted)
            return $this->helpers->formattedNumber($summ);
        else
            return $summ;
    }
}
