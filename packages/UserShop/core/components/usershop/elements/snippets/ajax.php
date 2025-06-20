<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (empty($data['ajax_connect']) || empty($data['action'])) return;

$pdoTools = $modx->getService("pdoTools");

switch ($data['action']) {
    case 'add-order-review':
        if (!$data['content']) exit(json_encode([
            "success" => false,
            "message" => "Не передан content"
        ]));
        if (strlen($data['content']) < 4) exit(json_encode([
            "success" => false,
            "message" => "Введите больше символов"
        ]));

        $modx->addPackage('usershop', $modx->getOption('core_path') . 'components/usershop/model/');

        $new_review = $modx->newObject('OrderReviews', [
            "user_id" => $modx->user->id,
            "order_id" => $data['order_id'],
            "content" => $data['content'],
            "created_at" => date('Y-m-d H:i:s')
        ]);

        if ($new_review->save()) {
            exit(json_encode([
                "success" => true,
                "message" => "Отзыв успешно отправлен"
            ]));
        } else {
            exit(json_encode([
                "success" => false,
                "message" => "Ошибка при создании отзыва"
            ]));
        }

    case 'add-ticket':
        if (strlen($data['subject']) < 4 || strlen($data['content']) < 4) exit(json_encode([
            "success" => false,
            "message" => "Введите больше символов"
        ]));

        $modx->addPackage('usershop', $modx->getOption('core_path') . 'components/usershop/model/');

        $new_review = $modx->newObject('UserTickets', [
            "user_id" => $modx->user->id,
            "subject" => $data['subject'],
            "content" => $data['content'],
            "created_at" => date('Y-m-d H:i:s')
        ]);

        if ($new_review->save()) {
            exit(json_encode([
                "success" => true,
                "message" => "Обращение успешно отправлено"
            ]));
        } else {
            exit(json_encode([
                "success" => false,
                "message" => "Ошибка при создании обращения"
            ]));
        }

    case 'add-product-reviews':
        $errors = [];
        if (strlen($data['content']) < 6)
            $errors["content"] = "Введите больше символов";
        if (empty($data['rating']) || !in_array((int)$data['rating'], [1, 2, 3, 4, 5]))
            $errors["rating"] = "Укажите рейтинг";
        if (empty($data['product_id']))
            $errors["product_id"] = "Укажите product_id";

        if (count($errors) > 0) {
            exit(json_encode([
                "success" => false,
                "message" => "В форме содержатся ошибки",
                "errors" => $errors
            ]));
        }

        $modx->addPackage('usershop', $modx->getOption('core_path') . 'components/usershop/model/');

        $params = [
            "product_id" => $data['product_id'],

            "defects" => $data['defects'],
            "advantages" => $data['advantages'],
            "content" => $data['content'],

            "rating" => $data['rating'],
            "created_at" => date('Y-m-d H:i:s')
        ];

        if ($modx->user->id) {
            $profile = $modx->user->getOne('Profile');

            $params["user_id"] = $modx->user->id;
            $params["author"] = $profile->fullname;
        }

        $new_review = $modx->newObject('UserProductReviews', $params);

        if ($new_review->save()) {
            exit(json_encode([
                "success" => true,
                "message" => "Отзыв отправлен на модерацию"
            ]));
        } else {
            exit(json_encode([
                "success" => false,
                "message" => "Ошибка при создании отзыва"
            ]));
        }
}
