id: 51
source: 1
name: msNewPrice
category: msnewprice
properties: 'a:12:{s:7:"product";a:7:{s:4:"name";s:7:"product";s:4:"desc";s:23:"msnewprice_prop_product";s:4:"type";s:11:"numberfield";s:7:"options";a:0:{}s:5:"value";s:0:"";s:7:"lexicon";s:21:"msnewprice:properties";s:4:"area";s:0:"";}s:8:"tplOuter";a:7:{s:4:"name";s:8:"tplOuter";s:4:"desc";s:24:"msnewprice_prop_tplOuter";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:20:"tpl.msnewprice.outer";s:7:"lexicon";s:21:"msnewprice:properties";s:4:"area";s:0:"";}s:12:"tplSubscribe";a:7:{s:4:"name";s:12:"tplSubscribe";s:4:"desc";s:28:"msnewprice_prop_tplSubscribe";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:24:"tpl.msnewprice.subscribe";s:7:"lexicon";s:21:"msnewprice:properties";s:4:"area";s:0:"";}s:12:"tplStockInfo";a:7:{s:4:"name";s:12:"tplStockInfo";s:4:"desc";s:28:"msnewprice_prop_tplStockInfo";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:25:"tpl.msnewprice.stock.info";s:7:"lexicon";s:21:"msnewprice:properties";s:4:"area";s:0:"";}s:14:"tplNoStockInfo";a:7:{s:4:"name";s:14:"tplNoStockInfo";s:4:"desc";s:30:"msnewprice_prop_tplNoStockInfo";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:28:"tpl.msnewprice.no.stock.info";s:7:"lexicon";s:21:"msnewprice:properties";s:4:"area";s:0:"";}s:8:"tplEmpty";a:7:{s:4:"name";s:8:"tplEmpty";s:4:"desc";s:24:"msnewprice_prop_tplEmpty";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:0:"";s:7:"lexicon";s:21:"msnewprice:properties";s:4:"area";s:0:"";}s:14:"returnNewPrice";a:7:{s:4:"name";s:14:"returnNewPrice";s:4:"desc";s:30:"msnewprice_prop_returnNewPrice";s:4:"type";s:13:"combo-boolean";s:7:"options";a:0:{}s:5:"value";b:0;s:7:"lexicon";s:21:"msnewprice:properties";s:4:"area";s:0:"";}s:13:"showSubscribe";a:7:{s:4:"name";s:13:"showSubscribe";s:4:"desc";s:29:"msnewprice_prop_showSubscribe";s:4:"type";s:13:"combo-boolean";s:7:"options";a:0:{}s:5:"value";b:1;s:7:"lexicon";s:21:"msnewprice:properties";s:4:"area";s:0:"";}s:10:"dateFormat";a:7:{s:4:"name";s:10:"dateFormat";s:4:"desc";s:26:"msnewprice_prop_dateFormat";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:14:"%d %b %Y %H:%M";s:7:"lexicon";s:21:"msnewprice:properties";s:4:"area";s:0:"";}s:7:"showLog";a:7:{s:4:"name";s:7:"showLog";s:4:"desc";s:23:"msnewprice_prop_showLog";s:4:"type";s:13:"combo-boolean";s:7:"options";a:0:{}s:5:"value";b:0;s:7:"lexicon";s:21:"msnewprice:properties";s:4:"area";s:0:"";}s:6:"sortby";a:7:{s:4:"name";s:6:"sortby";s:4:"desc";s:22:"msnewprice_prop_sortby";s:4:"type";s:9:"textfield";s:7:"options";a:0:{}s:5:"value";s:29:"msnewpricedata.msn_product_id";s:7:"lexicon";s:21:"msnewprice:properties";s:4:"area";s:0:"";}s:13:"toPlaceholder";a:7:{s:4:"name";s:13:"toPlaceholder";s:4:"desc";s:29:"msnewprice_prop_toPlaceholder";s:4:"type";s:13:"combo-boolean";s:7:"options";a:0:{}s:5:"value";b:0;s:7:"lexicon";s:21:"msnewprice:properties";s:4:"area";s:0:"";}}'
static_file: core/components/msnewprice/elements/snippets/snippet.msnewprice.php

-----

if (!$msnewprice = $modx->getService('msnewprice', 'msnewprice', $modx->getOption('msnewprice_core_path', null,
        $modx->getOption('core_path') . 'components/msnewprice/') . 'model/msnewprice/', $scriptProperties)
) {
    return 'Could not load msnewprice class!';
}
$msnewprice->initialize($modx->context->key, $scriptProperties);
if (empty($tplOuter)) {
    $tplOuter = 'tpl.msnewprice.outer';
}
if (empty($tplSubscribe)) {
    $tplSubscribe = 'tpl.msnewprice.subscribe';
}
if (empty($tplSubscribeInfo)) {
    $tplSubscribeInfo = 'tpl.msnewprice.subscribe.info';
}
if (empty($tplStockInfo)) {
    $tplStockInfo = 'tpl.msnewprice.stock.info';
}
if (empty($tplNoStockInfo)) {
    $tplStockInfo = 'tpl.msnewprice.no.stock.info';
}
if (empty($list)) {
    $list = 'default';
}
if (empty($user_id)) {
    $user_id = $modx->user->id;
}
if (isset($product) && $product === '') {
    $product = $modx->resource->id;
}
$class = 'msnewpricedata';
// Start building "Where" expression
$where = array();
$where[$class . '.msn_product_id'] = $product;
// leftJoin
$leftJoin = array(
    array(
        'class' => 'msnewpricelist',
        'alias' => 'msnewpricelist',
        'on'    => '`msnewpricelist`.`res_id`=' . $product . ' AND `msnewpricelist`.`list`="' . $list . '" AND `msnewpricelist`.`user_id`=' . $user_id
    ),
);
// select
$select = array(
    'msnewpricedata' => implode(',', array_keys($modx->getFieldMeta('msnewpricedata'))),
    'msnewpricelist' => implode(',', array_keys($modx->getFieldMeta('msnewpricelist'))),
);
// Add custom parameters
foreach (array('where', 'leftJoin', 'select') as $v) {
    if (!empty($scriptProperties[$v])) {
        $tmp = $modx->fromJSON($scriptProperties[$v]);
        if (is_array($tmp)) {
            $$v = array_merge($$v, $tmp);
        }
    }
    unset($scriptProperties[$v]);
}
// default
$default = array(
    'class'             => $class,
    'leftJoin'          => $modx->toJSON($leftJoin),
    'where'             => $modx->toJSON($where),
    'select'            => $modx->toJSON($select),
    'sortby'            => $class . '.id',
    'sortdir'           => 'ASC',
    'fastMode'          => false,
    'return'            => !empty($returnIds) ? 'ids' : 'data',
    'nestedChunkPrefix' => 'msnewprice_',
    'disableConditions' => true
);
// Merge all properties and run!
$msnewprice->pdoTools->addTime('Query parameters ready');
$msnewprice->pdoTools->setConfig(array_merge($default, $scriptProperties), false);
$data = $msnewprice->pdoTools->run();
$data = $data[0];
// 
$data['res_id'] = $product;
$data['list'] = $list;
if (!is_array($data) || empty($data['msn_product_id'])) {
    return !empty($tplEmpty)
        ? $msnewprice->getChunk($tplEmpty, $scriptProperties)
        : '';
}
$output = '';
$data['msn_newprice_format'] = $msnewprice->formatPrice($data['msn_newprice']);
if (!empty($returnNewPrice)) {
    return $data['msn_newprice'];
}
// action 
$data['start_action'] = $msnewprice->dateFormat($data['msn_startdate'], $dateFormat);
$data['stop_action'] = $msnewprice->dateFormat($data['msn_stopdate'], $dateFormat);
if (!empty($data['msn_action'])) {
    $output .= empty($tplStockInfo)
        ? $msnewprice->getChunk('', $data)
        : $msnewprice->getChunk($tplStockInfo, $data, $msnewprice->pdoTools->config['fastMode']);
} else {
    $output .= empty($tplNoStockInfo)
        ? $msnewprice->getChunk('', $data)
        : $msnewprice->getChunk($tplNoStockInfo, $data, $msnewprice->pdoTools->config['fastMode']);
}
// showSubscribe
if ($showSubscribe && !empty($user_id)) {
    $data['list_added'] = !empty($data['id'])
        ? 'added'
        : '';
    $output .= empty($tplStockInfo)
        ? $msnewprice->getChunk('', $data)
        : $msnewprice->getChunk($tplSubscribe, $data, $msnewprice->pdoTools->config['fastMode']);
}
// log
if ($modx->user->hasSessionContext('mgr') && !empty($showLog)) {
    $log .= '<pre class="Log">' . print_r($msnewprice->pdoTools->getTime(), 1) . '</pre>';
    $output .= $log;
}
if (!empty($tplOuter)) {
    $array = array_merge($data, array('output' => $output));
    $output = $msnewprice->pdoTools->getChunk($tplOuter, $array, $msnewprice->pdoTools->config['fastMode']);
}
if (!empty($toPlaceholder)) {
    $modx->setPlaceholder($toPlaceholder, $output);
} else {
    $modx->regClientScript('<script type="text/javascript">Msnewprice.add.initialize(".msnewprice-default");</script>',
        true);

    return $output;
}