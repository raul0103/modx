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
        // case "get-user-discount":
        //     $user_id = $modx->user->id;
        //     if (!$user_id) return [
        //         "success" => false,
        //         "message" => "User not autorization"
        //     ];

        //     $modx->addPackage('usershop', $modx->getOption('core_path') . 'components/usershop/model/');
        //     $row = $modx->getObject('UserDiscount', [
        //         'user_id' => $user_id
        //     ]);

        //     exit("discount {$row->discount}");
}
