<?php


class msOptionsPriceMsOnGetProductPrice extends msOptionsPricePlugin
{
    public function run()
    {
        if ($this->modx->context->key == 'mgr') {
            return;
        }

        /** @var  msProduct $product */
        $product = $this->modx->getOption('product', $this->scriptProperties);
        if (
            !$product
            OR
            !($product instanceof xPDOObject)
        ) {
            return;
        }

        $rid = $product->get('id');
        $data = $this->modx->getOption('data', $this->scriptProperties, array(), true);
        $returned = (array)$this->modx->getPlaceholder('_returned_price');
        $options = (array)$this->modx->getOption('msoptionsprice_options', $data, array(), true);

        if (
            empty($options)
            AND
            (isset($returned['id']) AND $returned['id'] == $rid)
        ) {
            $options = (array)$this->modx->getOption('msoptionsprice_options', $returned, array(), true);
        } else {
            if (empty($options)) {
                $options = (array)$this->modx->getOption('options', $_REQUEST, array(), true);
            }
        }

        if (
            !$price = $this->modx->getOption('price', $returned)
            OR
            !isset($returned['id'])
            OR
            $returned['id'] != $rid
        ) {
            $price = $this->modx->getOption('price', $this->scriptProperties, 0, true);
        }


        $queryOptions = $options;
        $excludeIds = $excludeType = array(0);

        $cost = $price;
        while (
        $modification = $this->msoptionsprice->getModificationByOptions($rid, $queryOptions, null, $excludeIds,
            $excludeType)
        ) {

            $excludeIds[] = $modification['id'];
            $cost = $this->msoptionsprice->getCostByModification($rid, $price, $modification);
            if ($cost !== false) {
                $price = $cost;
            }
        }

        $returned['id'] = $rid;
        $returned['msoptionsprice_options'] = $options;
        $this->modx->event->returnedValues['price'] = $returned['price'] = $cost;
        $this->modx->setPlaceholder('_returned_price', $returned);

    }
}