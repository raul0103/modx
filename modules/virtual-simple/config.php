<?php

$config = [];

// Определени домена/поддомена
$host_explode = explode('.', $_SERVER['HTTP_HOST']);
$config['host_explode'] = $host_explode;
$subdomain = $host_explode[0];
$config['subdomain'] = $subdomain;


/**
 * Формируем массив доменов и их контекста
 * Получаем список контекстов по папкам в ./json
 * 
 * $contexts = [
 *  'domain' => 'web',
 *  'domain' => 'spb'
 * ]
 * 
 * >>>
 */
// $base_directory = __DIR__ . "/json";
// $contexts = [];

// $subdirectories = array_filter(scandir($base_directory), function ($dir) use ($base_directory) {
//     return is_dir($base_directory . DIRECTORY_SEPARATOR . $dir) && !in_array($dir, ['.', '..']);
// });

// foreach ($subdirectories as $subdir) {
//     $path = $base_directory . DIRECTORY_SEPARATOR . $subdir;

//     // Получаем только файлы внутри текущей поддиректории
//     $files = array_filter(scandir($path), function ($file) use ($path) {
//         return is_file($path . DIRECTORY_SEPARATOR . $file);
//     });

//     foreach ($files as $file) {
//         $filename = str_replace('.json', '', $file);
//         $contexts[$filename] = $subdir;
//     }
// }
// <<<




// >>> Ищем файл по поддомену и к какому контексту он относится
$base_directory = __DIR__ . "/json";

$target_file = "$subdomain.json";
$found_context = null;

// Получаем список поддиректорий
$subdirectories = array_filter(scandir($base_directory), function ($dir) use ($base_directory) {
    return is_dir($base_directory . DIRECTORY_SEPARATOR . $dir) && !in_array($dir, ['.', '..']);
});

// Ищем файл в каждой поддиректории
foreach ($subdirectories as $subdir) {
    $path = $base_directory . DIRECTORY_SEPARATOR . $subdir;

    // Проверяем, существует ли файл в этой поддиректории
    if (file_exists($path . DIRECTORY_SEPARATOR . $target_file)) {
        $found_context = $subdir;
        break;
    }
}

// if ($found_context !== null) {
//     echo "Файл '$target_file' находится в папке: $found_context" . PHP_EOL;
// } else {
//     echo "Файл '$target_file' не найден в поддиректориях." . PHP_EOL;
// }
// <<<

// >>> Подключаем контекст в modx
if ($found_context !== null) {
    $config['context_key'] = $found_context;
    // подмена контекста в соответсвии с доменом/поддоменом
    $modx->switchContext($config['context_key']);
} else {
    // $config['context_key'] = $modx->context->key;
    header("HTTP/1.1 400 Bad Request");
    exit;
}
// <<<

return $config;
