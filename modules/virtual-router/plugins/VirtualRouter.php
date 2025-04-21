<?php

/**
 * Определяет виртуальные поддомены, и производит подмену топонимов и данных
 * OnWebPagePrerender - Системное событие. Доступен контент страницы $modx->resource->_output
 * OnHandleRequest - Системное событие. Доступна запись плейсхолдера $modx->setPlaceholder
 */

if ($modx->context->key == 'mgr') return;

$module_path = MODX_BASE_PATH . "core/elements/modules/virtual-router";

// Основная логика модуля
include "$module_path/index.php";
$virtual_router = new VirtualRouter();

// Данные для подмены на странице
$changes = $virtual_router->getChangesData($modx->context->key);
if (!empty($modx->resource->_output) && is_array($changes)) {
    $output = &$modx->resource->_output;

    // Получаем шаблон из ключей (экранированных), сортируем по убыванию длины, чтобы избежать перекрытий
    $search = array_keys($changes);
    usort($search, function ($a, $b) {
        return mb_strlen($b) - mb_strlen($a);
    });

    $pattern = '/' . implode('|', array_map(function ($key) {
        return preg_quote($key, '/');
    }, $search)) . '/u';

    $output = preg_replace_callback($pattern, function ($matches) use ($changes) {
        return $changes[$matches[0]] ?? $matches[0];
    }, $output);
}
