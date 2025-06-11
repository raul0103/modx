<?php

// $order = $modx->getObject('msOrder', 44);
// $output = array(
//     'order' => $order->toArray(),
//     'user' => $order->User->toArray(),
//     'profile' => $order->UserProfile->toArray(),
//     'address' => $order->Address->toArray(),
//     'delivery' => $order->Delivery->toArray(),
//     'payment' => $order->Payment->toArray(),
//     'status' => $order->Status->toArray(),
//     'products' => []
// );
// foreach ($order->Products as $product) {
//     $output['products'][] = $product->toArray();
// }

// print_r($output);


$user_id = $modx->getPlaceholder('modx.user.id');
// Получаем пользователя
$user = $modx->getObject('modUser', $user_id);
if (!$user) {
    return 'Пользователь не найден';
}

// Получаем заказы этого пользователя
$q = $modx->newQuery('msOrder');
$q->where([
    'user_id' => $user->get('id')
]);
$q->sortby('createdon', 'DESC');
$orders = $modx->getCollection('msOrder', $q);

// Получаем поля по товарам в заказе
foreach ($orders as $order) {
    $order_products_by_id = [];
    foreach ($order->Products as $product) {
        $order_products_by_id[$product->product_id] = $product->toArray();
    }

    $products = $modx->getCollection('msProduct', ['id:in' => array_keys($order_products_by_id)]);

    $output = [];
    foreach ($products as $product) {
        $order_product = $order_products_by_id[$product->id];

        $output[] = [
            "pagetitle" => $order_product['name'],
            "price" => $order_product['price'],
            "count" => $order_product['count'],
            "options" => $order_product['options'],

            "thumb" => $product->get('thumb'),
            "unit" => $product->get('unit'),
        ];
    }

    $order->set('ProductsArray', $output);
}


return $orders;
