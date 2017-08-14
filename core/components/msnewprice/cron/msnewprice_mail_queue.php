<?php
/*
 * from https://github.com/bezumkin/Tickets/blob/master/core/components/tickets/cron/mail_queue.php
 *
 * рассылка писем из очереди
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
$q = $modx->newQuery('msnewpricequeue');
$q->sortby('timestamp', 'ASC');
$queue = $modx->getCollection('msnewpricequeue', $q);
/* @var msnewpricequeue $letter */
foreach ($queue as $letter) {
    if ($letter->Send()) {
        $letter->remove();
    }
}