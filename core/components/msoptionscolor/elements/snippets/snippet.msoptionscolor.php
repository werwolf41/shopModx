<?php

$msoptionscolor = $modx->getService('msoptionscolor', 'msoptionscolor',
    $modx->getOption('msoptionscolor_core_path', null,
        $modx->getOption('core_path') . 'components/msoptionscolor/') . 'model/msoptionscolor/', $scriptProperties);
if (!($msoptionscolor instanceof msoptionscolor)) {
    return '';
}
/* @var array $scriptProperties */
$msoptionscolor->initialize($modx->context->key, $scriptProperties);
/* @var pdoFetch $pdoFetch */
if (!$modx->loadClass('pdofetch', MODX_CORE_PATH . 'components/pdotools/model/pdotools/', false, true)) {
    return false;
}
$pdoFetch = new pdoFetch($modx, $scriptProperties);
if (empty($product) && !empty($input)) {
    $product = $input;
}
if (empty($selected)) {
    $selected = '';
}
if (empty($outputSeparator)) {
    $outputSeparator = "\n";
}
if ((empty($name) || $name == 'id') && !empty($options)) {
    $name = $options;
}
//
if (!$msopOption = $modx->getObject('msopOption', array('key' => $name))) {
    return;
}
$_option = $msopOption->get('id');
//
$output = '';
$product = !empty($product) ? $modx->getObject('msProduct', $product) : $product = $modx->resource;
$product_id = $product->get('id');
if (!($product instanceof msProduct)) {
    $output = 'This resource is not instance of msProduct class.';
} elseif (!empty($name) && $options = $product->get($name)) {
    if (!is_array($options) || $options[0] == '') {
        $output = !empty($tplEmpty)
            ? $pdoFetch->getChunk($tplEmpty, $scriptProperties)
            : '';
    } else {
        $rows = array();
        foreach ($options as $value) {
            $q = $modx->newQuery('msopColor');
            $q->where(array('product_id' => $product_id, 'option' => $_option, 'value' => $value));
            if (!empty($active)) {
                $q->andCondition(array('active' => 1));
            }
            $q->select('color,pattern');
            $q->limit(1);
            if ($q->prepare() && $q->stmt->execute()) {
                $msoc = $q->stmt->fetchAll(PDO::FETCH_ASSOC);
                $msoc = $msoc[0];
            } else {
                $msoc = array();
            }
            if (empty($msoc)) {
                continue;
            }
            $pls = array(
                'value'    => $value,
                'selected' => $value == $selected ? 'selected' : '',
                'color'    => $msoc['color'],
                'pattern'  => $msoc['pattern'],
            );
            $rows[] = empty($tplRow) ? $value : $pdoFetch->getChunk($tplRow, $pls);
        }
        $rows = implode($outputSeparator, $rows);
        $output = empty($tplOuter)
            ? $pdoFetch->getChunk('', array('name' => $name, 'rows' => $rows))
            : $pdoFetch->getChunk($tplOuter, array_merge($scriptProperties, array('name' => $name, 'rows' => $rows)));
    }
}
return $output;