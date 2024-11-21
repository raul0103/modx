<?php

/**
 * Скрипт для блокировки спама
 * Сохраняет данные в callbacks.json
 */

function getUserIP()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        // IP из заголовка HTTP_CLIENT_IP
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // IP из заголовка HTTP_X_FORWARDED_FOR (используется прокси)
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        // IP из REMOTE_ADDR
        return $_SERVER['REMOTE_ADDR'];
    }
}

// Конфигурация
$file_path = MODX_BASE_PATH . '/callbacks.json'; // Путь к JSON-файлу
$limits = [
    'time' => 60 * 60 * 4, // 4 часа
    'count' => 2           // Лимит отправок формы
];

// Текущий IP пользователя
$user_ip = getUserIP();

// Загрузка данных из файла
if (!file_exists($file_path)) {
    $data = [
        'created_at' => date('Y-m-d'), // Дата создания файла
        'ips' => []                   // Список IP с данными
    ];
    file_put_contents($file_path, json_encode($data, JSON_PRETTY_PRINT));
} else {
    $data = json_decode(file_get_contents($file_path), true);
}

// Проверка даты создания файла, удаление, если файл устарел (один день)
if (date('Y-m-d') !== $data['created_at']) {
    $data = [
        'created_at' => date('Y-m-d'),
        'ips' => []
    ];
    file_put_contents($file_path, json_encode($data, JSON_PRETTY_PRINT));
}

// Проверка IP пользователя
$is_spam = false;
if (isset($data['ips'][$user_ip])) {
    $ip_data = $data['ips'][$user_ip];
    $time_diff = time() - $ip_data['time'];

    // Если прошло больше 4 часов, удалить запись
    if ($time_diff > $limits['time']) {
        unset($data['ips'][$user_ip]);
    } else {
        // Увеличить счетчик отправок и проверить лимит
        $ip_data['count']++;
        $data['ips'][$user_ip] = $ip_data;

        if ($ip_data['count'] > $limits['count']) {
            $is_spam = true;
        }
    }
} else {
    // Добавить нового пользователя
    $data['ips'][$user_ip] = [
        'time' => time(),
        'count' => 1
    ];
}

// Сохранение обновленных данных в файл
file_put_contents($file_path, json_encode($data, JSON_PRETTY_PRINT));

// Вернуть результат проверки
if ($is_spam) {
    return false; // Это спам
}
