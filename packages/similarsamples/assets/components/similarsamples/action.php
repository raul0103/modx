<?php

/**
 * Обработка AJAX запросов
 */

if (empty($_REQUEST['action'])) {
    exit(json_encode(array('success' => false, 'message' => 'Access denied')));
} else {
    $action = $_REQUEST['action'];
}

/** @var modX $modx */
define('MODX_API_MODE', true);
/** @noinspection PhpIncludeInspection */
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/index.php';

switch ($action) {
}
