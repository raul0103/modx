<?php
if ($modx->context->key === 'mgr') return;

$valid_username = 'admin';
$valid_password = 'admin$$';

if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="Restricted Area"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Authorization Required';
    exit;
} else {
    if (
        $_SERVER['PHP_AUTH_USER'] !== $valid_username ||
        $_SERVER['PHP_AUTH_PW'] !== $valid_password
    ) {
        header('WWW-Authenticate: Basic realm="Restricted Area"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'Invalid Credentials';
        exit;
    }
}
