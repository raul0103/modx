<?php
$config = [];

// Определени домена/поддомена
$subdomain = explode('.', $_SERVER['HTTP_HOST'])[0];
$config['subdomain'] = $subdomain;

// Определение контекста
$contexts = [
    'beton-zavod-spb' => 'spb',
    'beton-volkhov-zavod' => 'spb',

    'beton-v-moskve' => 'web',
    'beton-ramenskoe-zavod' => 'web'
];
if (isset($contexts[$subdomain])) {
    $config['context_key'] = $contexts[$subdomain];
    // подмена контекста в соответсвии с доменом/поддоменом
    $modx->switchContext($config['context_key']);
} else {
    $config['context_key'] = $modx->context->key;
}

return $config;
