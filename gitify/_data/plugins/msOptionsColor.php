id: 12
source: 1
name: msOptionsColor
category: msoptionscolor
properties: null
static_file: core/components/msoptionscolor/elements/plugins/plugin.msoptionscolor.php

-----

$msoptionscolor = $modx->getService('msoptionscolor', 'msoptionscolor',
    $modx->getOption('msoptionscolor_core_path', null,
        $modx->getOption('core_path') . 'components/msoptionscolor/') . 'model/msoptionscolor/', $scriptProperties);
if (!($msoptionscolor instanceof msoptionscolor)) {
    return '';
}

$eventName = $modx->event->name;
if (method_exists($msoptionscolor, $eventName) && $msoptionscolor->active) {
    $msoptionscolor->$eventName($scriptProperties, $product);
}