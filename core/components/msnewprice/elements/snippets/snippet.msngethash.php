<?php
if (!$msnewprice = $modx->getService('msnewprice', 'msnewprice', $modx->getOption('msnewprice_core_path', null,
        $modx->getOption('core_path') . 'components/msnewprice/') . 'model/msnewprice/', $scriptProperties)
) {
    return 'Could not load msnewprice class!';
}
$msnewprice->initialize($modx->context->key, $scriptProperties);

if (empty($list)) {
    $list = 'default';
}
if (empty($user_id)) {
    $user_id = 0;
}
if (isset($resource) && $resource === '') {
    $resource = 0;
}
$class = 'msnewpricelist';

$where = array();
$where[$class . '.res_id'] = $resource;
$where[$class . '.list'] = $list;
$where[$class . '.user_id'] = $user_id;

$select = array(
    'msnewpricelist' => implode(',', array_keys($modx->getFieldMeta('msnewpricelist'))),
);

// default
$default = array(
    'class'             => $class,
    'where'             => $modx->toJSON($where),
    'select'            => $modx->toJSON($select),
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
if (!is_array($data) || empty($data['hash'])) {
    return false;
} else {
    return $data['hash'];
}