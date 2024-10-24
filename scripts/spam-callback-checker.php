<?php

/**
 * Скрипт для блокировки спама
 * Можно поставить на срабатываение обратной формы
 */

$cookie = [
    'key' => 'callbacks',
    'default' =>  [
        'time' => time(),
        'count' => 1
    ] // Дефолные данные при создании куки
];

// Лимиты по времени указанные в секундах
$limits = [
    'time' => 60 * 60 * 4, // Секунды. Лимит действия блокировки (60*60*4 = 4 часа)
    'count' => 2 // Кол-во разрешенных отправок формы
];

// Если нет текущих данных  - создать
$is_spam = false;
if (empty($_COOKIE[$cookie['key']])) {
    setcookie($cookie['key'], json_encode($cookie['default']));
} else {
    $data = json_decode($_COOKIE[$cookie['key']], true);
    $data['count'] = $data['count'] + 1; // Увеличили кол-во отправок формы

    $difference = time() - $data['time'];

    // Если прошел промежуток блокировки - начать проверку заново
    if ($difference > $limits['time']) {
        setcookie($cookie['key'], json_encode($cookie['default']));
    } else {
        if ($data['count'] > $limits['count']) {
            $is_spam = true;
        }

        setcookie($cookie['key'], json_encode($data));
    }
}

if ($is_spam) return false;
