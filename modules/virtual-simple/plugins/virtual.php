<?php

/**
 * Определяет виртуальные поддомены, и производит подмену топонимов и данных
 * OnWebPagePrerender - Системное событие. Доступен контент страницы $modx->resource->_output
 * OnHandleRequest - Системное событие. Доступна запись плейсхолдера $modx->setPlaceholder
 */

if ($modx->context->key == 'mgr') return;

// $subdomain = explode('.', $_SERVER['HTTP_HOST'])[0];
// $context_key = $modx->context->key;

$config = include MODX_BASE_PATH . "core/elements/modules/virtual/config.php";

$json_data_path = [
    "current" => MODX_BASE_PATH . "core/elements/modules/virtual/json/{$config['context_key']}/{$config['subdomain']}.json",
    "default" => MODX_BASE_PATH . "core/elements/modules/virtual/json/{$config['context_key']}/_default.json"
];
$changes_path = MODX_BASE_PATH . "core/elements/modules/virtual/changes/{$config['context_key']}.php";
$changes_default_path = MODX_BASE_PATH . "core/elements/modules/virtual/changes/web.php";

$json_data = [];
foreach ($json_data_path as $key => $path) {
    if (!file_exists($path)) continue;

    $content = file_get_contents($path);
    if ($content !== false) {
        $json_data[$key] = get_object_vars(json_decode($content));
    }
}

// Объеденяем данные поддомена с дефолтными
if (!empty($json_data["current"])) {
    $json_data = array_replace_recursive($json_data["default"], $json_data["current"]);
} else {
    $json_data = $json_data['default'];
}

if (empty($json_data)) {
    return;
}

// Подмена топонимов
if (file_exists($changes_path)) {
    $changes = include $changes_path;
} else {
    $changes = include $changes_default_path;
}

if (isset($modx->resource->_output)) {
    $output = &$modx->resource->_output;
    $pattern = '/' . implode('|', array_map('preg_quote', array_keys($changes))) . '/u';
    $output = preg_replace_callback($pattern, function ($matches) use ($changes) {
        return $changes[$matches[0]];
    }, $output);
}

// base_url
if (count($config['host_explode']) > 2) {
    $json_data['base_url'] = "https://{$_SERVER['HTTP_HOST']}.ru/";
} else {
    $json_data['base_url'] = "https://{$config['subdomain']}.ru/";
}

$modx->setPlaceholder('virtual', $json_data);
