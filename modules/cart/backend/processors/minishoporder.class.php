<?php

/**
 * Работа с заказами miniShop2
 */

require_once dirname(__DIR__) . '/classes/main.class.php';

class minishoporder extends Main
{

    /**
     * Summary of process
     * @param mixed $form_data - Данные из формы (почта, номер телефона и тд)
     */
    public function process($form_data = null)
    {

        global $modx;

        $user_id = $modx->getPlaceholder('modx.user.id');
        if ($user_id) {
            $user = $modx->getObject('modUser', $user_id);
            $profile = $user->getOne('Profile');
        }

        $products = $this->getProducts();
        if (count($products) == 0)
            return [
                'success' => false,
                'message' => 'У пользователя нет товаров'
            ];

        if ($miniShop2 = $modx->getService('miniShop2')) {
            // Инициализируем класс в текущий контекст
            $miniShop2->initialize($modx->context->key);

            // 1. Добавляем товары в корзину
            foreach ($products as $product) {
                $miniShop2->cart->add($product['id'], $product['count'], [
                    'user_discount' => $form_data['user_discount']
                ]);
            }

            // 2. Добавляем данные пользователя в заказ
            if ($form_data) {
                $miniShop2->order->add('receiver', $form_data['name']);
                $miniShop2->order->add('email', $form_data['email']);
                $miniShop2->order->add('phone', $form_data['phone']);
                $miniShop2->order->add('street', $form_data['street']);
                $miniShop2->order->add('city', $form_data['city']);
                $miniShop2->order->add('comment', $form_data['comment']);
            }
            if ($profile) {
                $miniShop2->order->add('email', $profile->email);
                $miniShop2->order->add('receiver', $profile->fullname);
            }

            // 3. Выбор доставки и оплаты (ID из админки → miniShop2 → Способы доставки / оплаты)
            $miniShop2->order->add('delivery', 1);
            $miniShop2->order->add('payment', 1);

            // 4. Отправка заказа
            $response = $miniShop2->order->submit();
            if (!$response->success) {
                return [
                    'success' => false,
                    'message' => 'Ошибка при добавлении товаров в корзину'
                ];
            }
        }
    }
}
