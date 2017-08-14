<?php
if (!$msnewprice = $modx->getService('msnewprice', 'msnewprice', $modx->getOption('msnewprice_core_path', null,
        $modx->getOption('core_path') . 'components/msnewprice/') . 'model/msnewprice/', $scriptProperties)
) {
    return 'Could not load msnewprice class!';
}
$msnewprice->initialize($modx->context->key, $scriptProperties);

$hash = $_GET['hash'];
if (empty($tplUserInfo)) {
    $tplUserInfo = 'tpl.msnewprice.del.subscribe.user.info';
}
if (empty($tplNoUserInfo)) {
    $tplNoUserInfo = 'tpl.msnewprice.del.subscribe.no.user.info';
}
if (empty($tplNotYourResource)) {
    $tplNotYourResource = 'tpl.msnewprice.del.subscribe.not.your.resource';
}
if (empty($tplResult)) {
    $tplResult = 'tpl.msnewprice.del.subscribe.result';
}
if (empty($user_id)) {
    $user_id = $modx->user->id;
}

$class = 'msnewpricelist';
// where
$where = array();
$where[$class . '.hash'] = $hash;
// leftJoin
$leftJoin = array(
    array(
        'class' => 'modUserProfile',
        'alias' => 'modUserProfile',
        'on'    => '`modUserProfile`.`internalKey`=`msnewpricelist`.`user_id`'
    ),
);
// select
$select = array(
    'msnewpricelist' => implode(',', array_keys($modx->getFieldMeta('msnewpricelist'))),
    'modUserProfile' => implode(',', array_keys($modx->getFieldMeta('modUserProfile'))),
);
// default
$default = array(
    'class'             => $class,
    'where'             => $modx->toJSON($where),
    'select'            => $modx->toJSON($select),
    'leftJoin'          => $modx->toJSON($leftJoin),
    'return'            => 'data',
    'fastMode'          => false,
    'nestedChunkPrefix' => 'msnewprice_',
    'disableConditions' => true
);
// Merge all properties and run!
$msnewprice->pdoTools->addTime('Query parameters ready');
$msnewprice->pdoTools->setConfig($default, false);
$data = $msnewprice->pdoTools->run();
$data = $data[0];
// is empty data
if (!is_array($data)) {
    return !empty($tplEmpty)
        ? $msnewprice->getChunk($tplEmpty, $scriptProperties)
        : '';
}
$output = '';
$scriptProperties['resources'] = $data['res_id'];
// user
if (!empty($user_id)) {
    $output .= empty($tplUserInfo)
        ? $msnewprice->getChunk('', $data)
        : $msnewprice->getChunk($tplUserInfo, $data, $msnewprice->pdoTools->config['fastMode']);
} // no user
else {
    $output .= empty($tplNoUserInfo)
        ? $msnewprice->getChunk('', $data)
        : $msnewprice->getChunk($tplNoUserInfo, $data, $msnewprice->pdoTools->config['fastMode']);
}
// resource
if (!empty($data['res_id'])) {
    if ($snippet = $modx->getObject('modSnippet', array('name' => $resSnippet))) {
        $properties = $snippet->getProperties();
        $scriptProperties = array_merge($properties, $scriptProperties);
        $output .= $msnewprice->processTags($snippet->process($scriptProperties));
    }
}
// user !== user_id
if (!empty($user_id) && $user_id != $data['user_id']) {
    $output .= empty($tplNotYourResource)
        ? $msnewprice->getChunk('', $data)
        : $msnewprice->getChunk($tplNotYourResource, $data, $msnewprice->pdoTools->config['fastMode']);
} // Result
elseif (!empty($user_id)) {
    // del hash
    if ($hash = $modx->getObject('msnewpricelist', array('hash' => $hash))) {
        if ($delSubscribe) {
            $hash->remove();
        }
        $output .= empty($tplResult)
            ? $msnewprice->getChunk('', $data)
            : $msnewprice->getChunk($tplResult, $data, $msnewprice->pdoTools->config['fastMode']);
    }
}
// log
if ($modx->user->hasSessionContext('mgr') && !empty($showLog)) {
    $log .= '<pre class="Log">' . print_r($msnewprice->pdoTools->getTime(), 1) . '</pre>';
    $output .= $log;
}
if (!empty($toPlaceholder)) {
    $modx->setPlaceholder($toPlaceholder, $output);
} else {
    return $output;
}