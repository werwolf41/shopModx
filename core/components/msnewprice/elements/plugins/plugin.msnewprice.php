<?php

$msnewprice = $modx->getService('msnewprice', 'msnewprice', $modx->getOption('msnewprice_core_path', null,
        $modx->getOption('core_path') . 'components/msnewprice/') . 'model/msnewprice/', $scriptProperties);
if (!($msnewprice instanceof msnewprice)) {
    return '';
}

$eventName = $modx->event->name;
if (method_exists($msnewprice, $eventName) && $msnewprice->active) {
    $eventName = lcfirst($eventName);
    $msnewprice->$eventName($scriptProperties, $product);
}