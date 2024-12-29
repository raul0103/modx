<?php

/**
 * Определяет виртуальные поддомены, и производит подмену топонимов и данных
 * OnWebPagePrerender - Системное событие. Доступен контент страницы $modx->resource->_output
 * OnHandleRequest - Системное событие. Доступна запись плейсхолдера $modx->setPlaceholder
 */

if ($modx->context->key == 'mgr') return;

$subdomain = explode('.', $_SERVER['HTTP_HOST'])[0];

$json_data_path = [
    "current" => MODX_BASE_PATH . 'core/elements/modules/virtual/json/' . $subdomain . '.json',
    "default" => MODX_BASE_PATH . 'core/elements/modules/virtual/json/_default.json'
];

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
    $json_data["current"]["subdomain"] = $subdomain;
    $json_data = array_replace_recursive($json_data["default"], $json_data["current"]);
} else {
    $json_data = $json_data['default'];
}

$modx->setPlaceholder('virtual', $json_data);

// Подмена топонимов
$changes = [
    "город Москва, Алтуфьевское ш, д. 79а" => "город Москва, Алтуфьевское ш, д. 79а", // То что не надо трогать. ЮР адрес на странице реквизиты
    "Москва" => $json_data["topo1"],
    "в Москве" => $json_data["topo2"],
    "по Москве" => $json_data["topo3"],
    "Москвы" => $json_data["topo4"],
];


if (isset($modx->resource->_output)) {
    $output = &$modx->resource->_output;
    $pattern = '/' . implode('|', array_map('preg_quote', array_keys($changes))) . '/u';
    $output = preg_replace_callback($pattern, function ($matches) use ($changes) {
        return $changes[$matches[0]];
    }, $output);
}
