<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$corePath = $modx->getOption('usershop.core_path', null, $modx->getOption('core_path') . 'components/usershop/');
require_once $corePath . 'model/usershop.class.php';

$modx->usershop = new UserShop($modx);
$modx->lexicon->load('usershop:default');

$path = $modx->getOption('processorsPath', $modx->usershop->config, $corePath . 'processors/');
$modx->getService('rest', 'rest.modRestClient');
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));
