<?php

require_once dirname(__DIR__) . '/classes/main.class.php';

class change extends Main
{
    /**
     * @param mixed $product_data
     * @return mixed $product_data - Обновленный массив данных
     */
    public function process($product_data)
    {

        $cart_items = $this->session->get();

        if (empty($cart_items)) {
            $cart_items = [];

            $cart_items[] = $product_data;
        } else {
            /**
             * Ищем товар в корзине
             * Если есть, то обновляем значения всех полей array_replace
             *      так как данные (цена, единица измерения и тд) могли измениться
             */
            $item_exist = false;
            foreach ($cart_items as &$cart_item) {
                if ($cart_item['id'] == $product_data['id']) {
                    $cart_item = array_replace($cart_item, $product_data);

                    $product_data['count'] = $cart_item['count'];

                    $item_exist = true;
                    break;
                }
            }

            // Если товара не было в массиве корзины, добавить
            if (!$item_exist) {
                $cart_items[] = $product_data;
            }
        }

        $product_data["summ"] =  $this->calcSumm($product_data['count'], $product_data['price']);

        $this->session->set($cart_items);

        $cart_total = $this->getCartTotal($cart_items);
        return [
            "product_data" => $product_data,
            "total" => [
                "count" => $cart_total["count"],
                "summ" => $cart_total["summ"],
                "old_summ" =>  $cart_total["old_summ"],
            ]
        ];
    }
}
