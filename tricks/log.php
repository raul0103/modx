<?php

/**
 * Подробный лог скрипта
 */

$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('FILE'); // Лог будет записан в core/cache/logs/error.log

// Тут код скрипта