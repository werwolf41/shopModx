<?php
/*
 * Акционная и новая цена, формирование писем пользователям
 */

define('MODX_API_MODE', true);
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/index.php';

$modx->getService('error', 'error.modError');
$modx->getRequest();
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');
$modx->error->message = null;

if (!$msnewprice = $modx->getService('msnewprice', 'msnewprice', $modx->getOption('msnewprice_core_path', null,
        $modx->getOption('core_path') . 'components/msnewprice/') . 'model/msnewprice/')
) {
    return 'Could not load msnewprice class!';
}

$now = date("Y-m-d H:i:s");
$list = 'default';

// оповещение о начале акции
$q = $modx->newQuery('msnewpricelist', array('list' => $list));
$q->leftJoin('msnewpricedata', 'msnewpricedata', 'msnewpricedata.msn_product_id = msnewpricelist.res_id');
$q->where(array(
    'msnewpricedata.msn_action:='     => 1,
    'msnewpricedata.msn_processed:='  => 0,
    'msnewpricedata.msn_startdate:<=' => $now,

));
$q->select($modx->getSelectColumns('msnewpricedata'));
$q->select($modx->getSelectColumns('msnewpricelist'));
$data = $modx->getIterator('msnewpricedata', $q);
$modx->lexicon->load('msnewprice:default');
$delsubscribe = $modx->getOption('msnewprice_res_delsubscribe', null, '');
foreach ($data as $d) {
    $pls = $d->toArray();
    $pls['delsubscribe'] = $delsubscribe;
    $subject = '';
    if ($chunk = $modx->newObject('modChunk', array('snippet' => $modx->lexicon('msnewprice_subject_action')))) {
        $chunk->setCacheable(false);
        $subject = $msnewprice->processTags($chunk->process($pls));
    }
    $body = 'no chunk set';
    if ($chunk = $modx->getObject('modChunk', $modx->getOption('msnewprice_chunk_action', $config, ''))) {
        $chunk->setCacheable(false);
        $body = $msnewprice->processTags($chunk->process($pls));
    }
    if (!empty($subject)) {
        $msnewprice->addQueue($pls['user_id'], $subject, $body, $email = '');
        if ($temp_id !== $pls['msn_product_id']) {
            $data_ = $modx->getObject('msnewpricedata', array('msn_product_id' => $pls['msn_product_id']));
            $data_->fromArray(array('msn_processed' => 1));
            $data_->save();
            $temp_id = $pls['msn_product_id'];
        }
    }
}

// для смены цены неакционному товару с новой ценой
$q = $modx->newQuery('msnewpricelist', array('list' => $list));
$q->leftJoin('msnewpricedata', 'msnewpricedata', 'msnewpricedata.msn_product_id = msnewpricelist.res_id');
$q->where(array(
    'msnewpricedata.msn_overwrite:='  => 1,
    'msnewpricedata.msn_processed:='  => 0,
    'msnewpricedata.msn_action:='     => 0,
    'msnewpricedata.msn_startdate:<=' => $now,
));
$q->select($modx->getSelectColumns('msnewpricedata'));
$q->select($modx->getSelectColumns('msnewpricelist'));
$data = $modx->getIterator('msnewpricedata', $q);
$modx->lexicon->load('msnewprice:default');
$delsubscribe = $modx->getOption('msnewprice_res_delsubscribe', null, '');
foreach ($data as $d) {
    $pls = $d->toArray();
    $pls['delsubscribe'] = $delsubscribe;
    $subject = '';
    if ($chunk = $modx->newObject('modChunk', array('snippet' => $modx->lexicon('msnewprice_subject_overwrite')))) {
        $chunk->setCacheable(false);
        $subject = $msnewprice->processTags($chunk->process($pls));
    }
    $body = 'no chunk set';
    if ($chunk = $modx->getObject('modChunk', $modx->getOption('msnewprice_chunk_newprice', $config, ''))) {
        $chunk->setCacheable(false);
        $body = $msnewprice->processTags($chunk->process($pls));
    }
    if (!empty($subject)) {
        $msnewprice->addQueue($pls['user_id'], $subject, $body, $email = '');
        if ($temp_id !== $pls['msn_product_id']) {
            if ($product = $modx->getObject('msProductData', array('id' => $pls['msn_product_id']))) {
                $product->set('price', $pls['msn_newprice']);
                $product->save();
            }
            $temp_id = $pls['msn_product_id'];
        }
    }
}

// для смены цены акционному товару с новой ценой
$q = $modx->newQuery('msnewpricelist', array('list' => $list));
$q->leftJoin('msnewpricedata', 'msnewpricedata', 'msnewpricedata.msn_product_id = msnewpricelist.res_id');
$q->where(array(
    'msnewpricedata.msn_overwrite:=' => 1,
    'msnewpricedata.msn_action:='    => 1,
    'msnewpricedata.msn_stopdate:<=' => $now,
));
$q->select($modx->getSelectColumns('msnewpricedata'));
$q->select($modx->getSelectColumns('msnewpricelist'));
$data = $modx->getIterator('msnewpricedata', $q);
foreach ($data as $d) {
    $pls = $d->toArray();
    if ($temp_id !== $pls['msn_product_id']) {
        if ($product = $modx->getObject('msProductData', array('id' => $pls['msn_product_id']))) {
            $product->set('price', $pls['msn_newprice']);
            $product->save();
        }
        $temp_id = $pls['msn_product_id'];
    }
}

// тут удаляем неакционные товары для которых выставили новую цену
$modx->removeCollection('msnewpricedata', array(
    'msn_overwrite'    => 1,
    'msn_action'       => 0,
    'msn_startdate:<=' => $now,
));

// удаляем акционные товары и акционные товары для которых выставили новую цену и дата каюк
$modx->removeCollection('msnewpricedata', array(
    'msn_action'      => 1,
    'msn_stopdate:<=' => $now,
));