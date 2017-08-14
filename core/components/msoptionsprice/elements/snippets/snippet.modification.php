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
/** @var pdoFetch $pdoFetch */
$pdoFetch = $modx->getService('pdoFetch');
$pdoFetch->setConfig($scriptProperties);
$pdoFetch->addTime('pdoTools loaded.');

$tpl = $scriptProperties['tpl'] = $modx->getOption('tpl', $scriptProperties, 'tpl.msOptionsPrice.modification', true);
$product = $scriptProperties['product'] = $modx->getOption('product', $scriptProperties, $modx->resource->id, true);
$type = $scriptProperties['type'] = $modx->getOption('type', $scriptProperties, 1, true);
$limit = $scriptProperties['limit'] = $modx->getOption('limit', $scriptProperties, 10, true);
$class = $scriptProperties['class'] = $modx->getOption('class', $scriptProperties, 'msopModification', true);
$outputSeparator = $scriptProperties['outputSeparator'] = $modx->getOption('outputSeparator', $scriptProperties, "\n",
    true);

$msoptionsprice->initialize($modx->context->key, $scriptProperties);

/** @var msProduct $product */
$product = $modx->getObject('msProduct', $product);
if (!$product OR !($product instanceof msProduct)) {
    return "[msOptionsPrice] The resource with id = {$product->id} is not instance of msProduct.";
}

$where = array(
    "{$class}.rid"     => $product->id,
    "{$class}.type:IN" => $msoptionsprice->explodeAndClean($type),
    "{$class}.active"  => true,
);
if (empty($showZeroPrice)) {
    $where["{$class}.price:>"] = 0;
}

$groupby = array(
    "{$class}.id",
);
$leftJoin = array(
    "msProduct"     => array(
        "class" => "msProduct",
        "on"    => "msProduct.id = {$class}.rid",
    ),
    "msProductData" => array(
        "class" => "msProductData",
        "on"    => "msProductData.id = {$class}.rid",
    ),
    "msVendor"      => array(
        "class" => "msVendor",
        "on"    => "msProductData.vendor = msVendor.id",
    ),
    "Option"        => array(
        "class" => "msopModificationOption",
        "on"    => "Option.mid = {$class}.id",
    ),
);
$innerJoin = array();
$select = array(
    $class          => $modx->getSelectColumns($class, $class),
    'msProduct'     => $modx->getSelectColumns('msProduct', 'msProduct', 'product_'),
    'msProductData' => $modx->getSelectColumns('msProductData', 'msProductData', 'data_'),
    'msVendor'      => $modx->getSelectColumns('msVendor', 'msVendor', 'vendor_'),
);

// Include thumbnails
if (!empty($includeThumbs)) {
    $thumbs = array_map('trim', explode(',', $includeThumbs));
    foreach ($thumbs as $thumb) {
        $leftJoin[$thumb] = array(
            'class' => 'msProductFile',
            'on'    => "`{$thumb}`.product_id = msProduct.id AND `{$thumb}`.rank = 0 AND `{$thumb}`.path LIKE '%/{$thumb}/%'",
        );

        $leftJoin["{$thumb}_image"] = array(
            'class' => 'msProductFile',
            'on'    => "`{$thumb}_image`.product_id = msProduct.id AND `{$thumb}_image`.parent = {$class}.image AND `{$thumb}_image`.path LIKE '%/{$thumb}/%'",
        );

        $select[$thumb] = "`{$thumb}`.url as `{$thumb}`";
        $select["{$thumb}_image"] = "`{$thumb}_image`.url as `{$thumb}_image`";
        $groupby[] = "`{$thumb}`.url";
    }
}

//

foreach (array('where', 'leftJoin', 'innerJoin', 'select', 'groupby') as $v) {
    if (!empty($scriptProperties[$v])) {
        $tmp = $scriptProperties[$v];
        if (!is_array($tmp)) {
            $tmp = json_decode($tmp, true);
        }
        if (is_array($tmp)) {
            $$v = array_merge($$v, $tmp);
        }
    }
    unset($scriptProperties[$v]);
}
$pdoFetch->addTime('Conditions prepared');

$default = array(
    'class'             => $class,
    'where'             => $where,
    'leftJoin'          => $leftJoin,
    'innerJoin'         => $innerJoin,
    'select'            => $select,
    'sortby'            => "CAST({$class}.price AS DECIMAL(10,2))",
    'sortdir'           => 'ASC',
    'groupby'           => implode(', ', $groupby),
    'return'            => !empty($returnIds) ? 'ids' : 'data',
    'nestedChunkPrefix' => 'minishop2_',
);

// Merge all properties and run!
$pdoFetch->setConfig(array_merge($default, $scriptProperties), false);
$rows = $pdoFetch->run();

// Process rows
$output = array();
if (!empty($rows) AND is_array($rows)) {
    foreach ($rows as $k => $row) {
        $opt_time_start = microtime(true);
        $options = $modx->call('msopModificationOption', 'getOptions', array(&$modx, $row['id'], $row['rid'], null));
        $row = array_merge($row, array('options' => $options));
        $opt_time += microtime(true) - $opt_time_start;

        $row['price'] = $product->getPrice(array('msoptionsprice_options' => $options));
        $row['weight'] = $product->getWeight(array('msoptionsprice_options' => $options));

        if (!empty($thumbs)) {
            foreach ($thumbs as $thumb) {
                if (!empty($row["{$thumb}_image"])) {
                    $row[$thumb] = $row["{$thumb}_image"];
                }
                unset($row["{$thumb}_image"]);
            }
        }
        $output[] = $pdoFetch->getChunk($tpl, $row);
        $rows[$k] = $row;
    }
    $pdoFetch->addTime('Time to load product modification options', $opt_time);
}

if ($scriptProperties['return'] == 'data') {
    return $rows;
}

$log = '';
if ($modx->user->hasSessionContext('mgr') AND !empty($showLog)) {
    $log .= '<pre class="msOptionsPriceLog">' . print_r($pdoFetch->getTime(), 1) . '</pre>';
}
// Return output
if (!empty($returnIds) AND is_string($rows)) {
    $modx->setPlaceholder('msOptionsPrice.log', $log);
    if (!empty($toPlaceholder)) {
        $modx->setPlaceholder($toPlaceholder, $rows);
    } else {
        return $rows;
    }
} else {
    $output['log'] = $log;
    $output = implode($outputSeparator, $output);
    if (!empty($tplWrapper) AND (!empty($wrapIfEmpty) OR !empty($output))) {
        $output = $pdoFetch->getChunk($tplWrapper, array(
            'output' => $output,
        ));
    }
    if (!empty($toPlaceholder)) {
        $modx->setPlaceholder($toPlaceholder, $output);
    } else {
        return $output;
    }
}