<?php

/**
 * Get an msopModification
 */
class msopModificationGetProcessor extends modProcessor
{
    public $objectType = 'msopModification';
    public $classKey = 'msopModification';
    public $languageTopics = array('msoptionsprice');
    public $permission = '';

    public function initialize()
    {
        return parent::initialize();
    }

    /** {@inheritDoc} */
    public function process()
    {
        /** @var msoptionsprice $msoptionsprice */
        $msoptionsprice = $this->modx->getService('msoptionsprice');
        $msoptionsprice->initialize($this->getProperty('ctx', $this->modx->context->key));

        $rid = (int)$this->getProperty('id');
        $iid = (int)$this->getProperty('iid');
        $options = (array)$this->getProperty('options', array());

        /** @var $product msProduct */
        if (!$product = $this->modx->getObject('msProduct', $rid)) {
            return $msoptionsprice->failure('', $this->getProperties());
        }

        if ($iid AND $modification = $msoptionsprice->getModificationByImage($rid, $iid, $options)) {
            $options = array_merge(
                $options,
                $this->modx->call('msopModificationOption', 'getOptions',
                    array(&$this->modx, $modification['id'], $modification['rid'], null))
            );
        } else {
            $modification = $msoptionsprice->getModificationByOptions($rid, $options);
        }

        if (!$modification) {
            $modification = $msoptionsprice->getModificationById(0, $rid);
        }

        $modification['cost'] = $product->getPrice(array('msoptionsprice_options' => $options));
        $modification['mass'] = $product->getWeight(array('msoptionsprice_options' => $options));

        if ($modification) {
            $options = $this->modx->call('msopModificationOption', 'getOptions',
                array(&$this->modx, $modification['id'], $modification['rid'], null));
        }

        $data = array(
            'rid'          => $rid,
            'modification' => $modification,
            'options'      => $options,
            'set'          => array(
                'options' => (bool)$iid,
            ),
            'errors'       => null
        );

        return $msoptionsprice->success('', $data);
    }

}

return 'msopModificationGetProcessor';