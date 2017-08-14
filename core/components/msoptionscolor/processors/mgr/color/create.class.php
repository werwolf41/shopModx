<?php

class msopColorCreateProcessor extends modObjectCreateProcessor
{
    public $classKey = 'msopColor';
    public $languageTopics = array('msoptionscolor:default', 'msoptionscolor:manager');
    public $permission = 'msoptionscolorsetting_save';

    /** {@inheritDoc} */
    public function initialize()
    {
        if (!$this->modx->hasPermission($this->permission)) {
            return $this->modx->lexicon('access_denied');
        }

        return parent::initialize();
    }

    /** {@inheritDoc} */
    public function beforeSet()
    {
        if ($this->modx->getObject('msopColor', array(
            'product_id' => $this->getProperty('product_id'),
            'option'     => $this->getProperty('option'),
            'value'      => $this->getProperty('value'),
        ))
        ) {
            return $this->modx->lexicon('msoptionscolor_err_non_unique');
        }

        return !$this->hasErrors();
    }

    /** {@inheritDoc} */
    public function beforeSave()
    {
        $c = $this->modx->newQuery('msopColor');
        $c->where(array(
            'product_id' => $this->getProperty('product_id'),
        ));

        $this->object->fromArray(array(
            'rank'   => $this->modx->getCount('msopColor', $c),
            'active' => true
        ));

        // ral
        if ($this->modx->getOption('msoptionscolor_active_ral', null, false)) {
            $ral = $this->getProperty('ral');
            if (!empty($ral) && $msopRal = $this->modx->getObject('msopRal', array('ral' => $ral))) {
                $this->object->set('color', $msopRal->get('html'));
            }
        }

        return parent::beforeSave();
    }
}

return 'msopColorCreateProcessor';