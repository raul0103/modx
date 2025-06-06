<?php
// assets/components/usershop/connector.php
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$corePath = $modx->getOption('usershop.core_path', null, $modx->getOption('core_path') . 'components/usershop/');
$modx->lexicon->load('usershop:default');

$path = $modx->getOption('processorsPath', array(), $corePath . 'processors/');
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));
