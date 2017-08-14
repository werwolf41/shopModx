<?php

/** @var array $scriptProperties */
$corePath = $modx->getOption('msoptionsprice_core_path', null,
    $modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/msoptionsprice/');
/** @var msoptionsprice $msoptionsprice */
$msoptionsprice = $modx->getService('msoptionsprice', 'msoptionsprice', $corePath . 'model/msoptionsprice/',
    array('core_path' => $corePath));
if (!$msoptionsprice) {
    return 'Could not load msoptionsprice class!';
}
$msoptionsprice->initialize($modx->context->key, $scriptProperties);
$msoptionsprice->loadResourceJsCss($scriptProperties);
