id: 53
source: 1
name: msOptionsColor
category: msoptionscolor
properties: "a:10:{s:7:\"product\";a:7:{s:4:\"name\";s:7:\"product\";s:4:\"desc\";s:27:\"msoptionscolor_prop_product\";s:4:\"type\";s:11:\"numberfield\";s:7:\"options\";a:0:{}s:5:\"value\";s:0:\"\";s:7:\"lexicon\";s:25:\"msoptionscolor:properties\";s:4:\"area\";s:0:\"\";}s:6:\"tplRow\";a:7:{s:4:\"name\";s:6:\"tplRow\";s:4:\"desc\";s:26:\"msoptionscolor_prop_tplRow\";s:4:\"type\";s:9:\"textfield\";s:7:\"options\";a:0:{}s:5:\"value\";s:22:\"tpl.msOptionsColor.row\";s:7:\"lexicon\";s:25:\"msoptionscolor:properties\";s:4:\"area\";s:0:\"\";}s:8:\"tplOuter\";a:7:{s:4:\"name\";s:8:\"tplOuter\";s:4:\"desc\";s:28:\"msoptionscolor_prop_tplOuter\";s:4:\"type\";s:9:\"textfield\";s:7:\"options\";a:0:{}s:5:\"value\";s:24:\"tpl.msOptionsColor.outer\";s:7:\"lexicon\";s:25:\"msoptionscolor:properties\";s:4:\"area\";s:0:\"\";}s:8:\"tplEmpty\";a:7:{s:4:\"name\";s:8:\"tplEmpty\";s:4:\"desc\";s:28:\"msoptionscolor_prop_tplEmpty\";s:4:\"type\";s:9:\"textfield\";s:7:\"options\";a:0:{}s:5:\"value\";s:0:\"\";s:7:\"lexicon\";s:25:\"msoptionscolor:properties\";s:4:\"area\";s:0:\"\";}s:4:\"name\";a:7:{s:4:\"name\";s:4:\"name\";s:4:\"desc\";s:24:\"msoptionscolor_prop_name\";s:4:\"type\";s:9:\"textfield\";s:7:\"options\";a:0:{}s:5:\"value\";s:0:\"\";s:7:\"lexicon\";s:25:\"msoptionscolor:properties\";s:4:\"area\";s:0:\"\";}s:8:\"selected\";a:7:{s:4:\"name\";s:8:\"selected\";s:4:\"desc\";s:28:\"msoptionscolor_prop_selected\";s:4:\"type\";s:9:\"textfield\";s:7:\"options\";a:0:{}s:5:\"value\";s:0:\"\";s:7:\"lexicon\";s:25:\"msoptionscolor:properties\";s:4:\"area\";s:0:\"\";}s:15:\"outputSeparator\";a:7:{s:4:\"name\";s:15:\"outputSeparator\";s:4:\"desc\";s:35:\"msoptionscolor_prop_outputSeparator\";s:4:\"type\";s:9:\"textfield\";s:7:\"options\";a:0:{}s:5:\"value\";s:1:\"\n\";s:7:\"lexicon\";s:25:\"msoptionscolor:properties\";s:4:\"area\";s:0:\"\";}s:6:\"active\";a:7:{s:4:\"name\";s:6:\"active\";s:4:\"desc\";s:26:\"msoptionscolor_prop_active\";s:4:\"type\";s:13:\"combo-boolean\";s:7:\"options\";a:0:{}s:5:\"value\";b:1;s:7:\"lexicon\";s:25:\"msoptionscolor:properties\";s:4:\"area\";s:0:\"\";}s:11:\"frontendCss\";a:7:{s:4:\"name\";s:11:\"frontendCss\";s:4:\"desc\";s:31:\"msoptionscolor_prop_frontendCss\";s:4:\"type\";s:9:\"textfield\";s:7:\"options\";a:0:{}s:5:\"value\";s:33:\"[[+assetsUrl]]css/web/default.css\";s:7:\"lexicon\";s:25:\"msoptionscolor:properties\";s:4:\"area\";s:0:\"\";}s:10:\"frontendJs\";a:7:{s:4:\"name\";s:10:\"frontendJs\";s:4:\"desc\";s:30:\"msoptionscolor_prop_frontendJs\";s:4:\"type\";s:9:\"textfield\";s:7:\"options\";a:0:{}s:5:\"value\";s:31:\"[[+assetsUrl]]js/web/default.js\";s:7:\"lexicon\";s:25:\"msoptionscolor:properties\";s:4:\"area\";s:0:\"\";}}"
static_file: core/components/msoptionscolor/elements/snippets/snippet.msoptionscolor.php

-----

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