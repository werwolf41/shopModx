id: 54
source: 1
name: msOptionsPrice.initialize
category: msOptionsPrice2
properties: 'a:3:{s:11:"frontendCss";a:7:{s:4:"name";s:11:"frontendCss";s:4:"desc";s:31:"msoptionsprice_prop_frontendCss";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:0:"";s:7:"lexicon";s:25:"msoptionsprice:properties";s:4:"area";s:0:"";}s:10:"frontendJs";a:7:{s:4:"name";s:10:"frontendJs";s:4:"desc";s:30:"msoptionsprice_prop_frontendJs";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:0:"";s:7:"lexicon";s:25:"msoptionsprice:properties";s:4:"area";s:0:"";}s:9:"actionUrl";a:7:{s:4:"name";s:9:"actionUrl";s:4:"desc";s:29:"msoptionsprice_prop_actionUrl";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:24:"[[+assetsUrl]]action.php";s:7:"lexicon";s:25:"msoptionsprice:properties";s:4:"area";s:0:"";}}'
static_file: core/components/msoptionsprice/elements/snippets/snippet.initialize.php

-----

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