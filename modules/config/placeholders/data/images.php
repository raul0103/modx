<?php

$data = [
    'logo' =>   [
        'value' => '/assets/template/images/logo/' . $modx->context->key . '.png',
        'show_error_message' => true
    ],
];

return [
    'config_prefix' => "images",
    'placeholders' => $data,
];
