<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("HTTP/1.1 403");
    exit();
}

define('MODX_API_MODE', true);
include $_SERVER['DOCUMENT_ROOT'] . '/index.php';

$module_path = MODX_CORE_PATH . 'elements/modules/simple-filter/';
require "$module_path/snippets/utils/sfCache.php";

$data = file_get_contents('php://input');
$data = json_decode($data, true);

$sfCache = new sfCache();
$scriptProperties = $sfCache->get("scriptProperties", $data['filter_uniqueid']);

if (empty($scriptProperties)) exit("Error get scriptProperties");

$scriptProperties['get_params'] = $data['get_params'];

$pdoTools = $modx->getService('pdoTools');
$result = $pdoTools->runSnippet("@FILE modules/simple-filter/snippets/getProducts.php", $scriptProperties);

exit($result);
