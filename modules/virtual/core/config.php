<?php

$module_base_path = dirname(__DIR__);
$domains = include "$module_base_path/domains.php";

/**
 * Логика формирования config файла.
 * НИЧЕГО НЕ ТРОГАЕМ
 */
$config = [];
if ($domains[$_SERVER['HTTP_HOST']]) {
    $domain = $_SERVER['HTTP_HOST'];

    $switch_context = true;
} else {
    $host_explode = explode('.', $_SERVER['HTTP_HOST']);
    $config['subdomain'] = $host_explode[0];

    // Определяем домен для получения контекста
    $domain = str_replace($config['subdomain'] . '.', '', $_SERVER['HTTP_HOST']);
}

$context_key = $domains[$domain];
$config['context_key'] = $context_key;

// Если это поддомен то проверяем наличие файла
if (!$switch_context) {
    $context_directory = "$module_base_path/json/$context_key/";
    if (file_exists("$context_directory/$target_file")) {
        $switch_context = true;
    }
}

// >>> Подключаем контекст в modx
if ($switch_context) {
    // подмена контекста в соответсвии с доменом/поддоменом
    $modx->switchContext($config['context_key']);
} else {
    header("HTTP/1.1 400 Bad Request");
    exit;
}
// <<<

return $config;
