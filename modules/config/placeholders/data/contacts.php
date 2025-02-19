<?php

$data = [
    'phone' => [
        'value' => '8 (999) 777-77-77',
        'show_error_message' => true
    ],
    'email' =>   [
        'value' => 'mailto@mail.ru',
        'show_error_message' => true
    ],
    'address' =>   [
        'value' => 'Санкт-Петербург, ул. Руставели, 13, офис 457',
        'show_error_message' => true
    ],
    'opening_hours' => [
        'value' => 'Ежедневно: 8:00 - 21:00',
        'show_error_message' => true
    ],
];

// Телефон без лишних символов
$data['phone_href'] = [
    'value' => preg_replace('/\D/', '', $data['phone']['value']),
    'show_error_message' => true
];

return [
    'config_prefix' => "contacts",
    'placeholders' => $data,
];
