<?php

//ini_set('display_errors', 1);
//ini_set('error_reporting', -1);

if (empty($_REQUEST['action'])) {
    @session_write_close();
    die('Access denied');
}
$_REQUEST['action'] = strtolower(ltrim($_REQUEST['action'], '/'));
define('MODX_API_MODE', true);
define('MODX_ACTION_MODE', true);

$productionIndex = dirname(dirname(dirname(dirname(__FILE__)))) . '/index.php';
$developmentIndex = dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/index.php';
if (file_exists($productionIndex)) {
    /** @noinspection PhpIncludeInspection */
    require_once $productionIndex;
} else {
    /** @noinspection PhpIncludeInspection */
    require_once $developmentIndex;
}
$modx->getService('error', 'error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');
$modx->error->message = null;
$ctx = !empty($_REQUEST['ctx']) ? $_REQUEST['ctx'] : 'web';
if ($ctx != 'web') {
    $modx->switchContext($ctx);
    $modx->user = null;
    $modx->getUser($ctx);
}

$corePath = $modx->getOption('msoptionsprice_core_path', null,
    $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/msoptionsprice/');
$msoptionsprice = $modx->getService('msoptionsprice', 'msoptionsprice', $corePath . 'model/msoptionsprice/',
    array('core_path' => $corePath));

if ($modx->error->hasError() OR !($msoptionsprice instanceof msoptionsprice)) {
    @session_write_close();
    die('Error');
}

$msoptionsprice->initialize($ctx);
$msoptionsprice->config['processorsPath'] = $msoptionsprice->config['processorsPath'] . 'web/';
if (!$response = $msoptionsprice->runProcessor($_REQUEST['action'], $_REQUEST)) {
    $response = $modx->toJSON(array(
        'success' => false,
        'code'    => 401,
    ));
}
@session_write_close();
echo $response;