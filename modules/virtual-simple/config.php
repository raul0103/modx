<?php

$config = [];

// Определение домена/поддомена
$host_explode = explode('.', $_SERVER['HTTP_HOST']);
$config['host_explode'] = $host_explode;
$subdomain = idn_to_utf8($host_explode[0], 0, INTL_IDNA_VARIANT_UTS46);
$config['subdomain'] = $subdomain;

$base_directory = __DIR__ . "/json";
$target_file = "$subdomain.json";
$found_context = null;

// Получаем список поддиректорий (контекстов)
$subdirectories = array_filter(scandir($base_directory), function ($dir) use ($base_directory) {
    return is_dir($base_directory . DIRECTORY_SEPARATOR . $dir) && !in_array($dir, ['.', '..']);
});

// Ищем файл поддомена
foreach ($subdirectories as $subdir) {
    $path = $base_directory . DIRECTORY_SEPARATOR . $subdir;

    if (file_exists($path . DIRECTORY_SEPARATOR . $target_file)) {
        $found_context = $subdir;
        break;
    }
}

// >>> Если не найден — ищем по настройкам MODX контекстов
if ($found_context === null && isset($modx)) {
    // Получаем все контексты
    $contexts = $modx->getCollection('modContext');

    $current_host = $_SERVER['HTTP_HOST'];

    /** @var modContext $context */
    foreach ($contexts as $context) {
        $ctxKey = $context->get('key');

        // Пропускаем системные контексты
        if (in_array($ctxKey, ['mgr'])) {
            continue;
        }

        // Получаем настройки контекста
        $settings = $context->getMany('ContextSettings');

        foreach ($settings as $setting) {
            if ($setting->get('key') === 'http_host') {
                $contextHost = $setting->get('value');

                if (strcasecmp($contextHost, $current_host) === 0) {
                    $found_context = $ctxKey;
                    break 2;
                }
            }
        }
    }
}

// >>> Переключаем контекст
if ($found_context !== null) {
    $config['context_key'] = $found_context;
    $modx->switchContext($found_context);
} else {
    header("HTTP/1.1 400 Bad Request");
    exit;
}

return $config;
