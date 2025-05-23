<?php
// assets/components/similarsamples/connector.php
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$corePath = $modx->getOption('similarsamples.core_path', null, $modx->getOption('core_path') . 'components/similarsamples/');
$modx->lexicon->load('similarsamples:default');

$path = $modx->getOption('processorsPath', array(), $corePath . 'processors/');
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));
