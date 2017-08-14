<?php


class msOptionsPriceMsOnBeforeAddToCart extends msOptionsPricePlugin
{
    public function run()
    {
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
        $options = (array)$this->modx->getOption('options', $this->scriptProperties, array(), true);

        $excludeIds = array(0);
        $excludeType = array(0, 2, 3);

        if ($modification = $this->msoptionsprice->getModificationByOptions($rid, $options, null, $excludeIds,
            $excludeType)
        ) {
            $options['modification'] = $modification['id'];
        }

        $this->modx->event->returnedValues['options'] = $options;
    }
}