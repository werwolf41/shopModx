<?php

class msopColorUpdateProcessor extends modObjectUpdateProcessor
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
            'value'      => $this->getProperty('value'),
            'id:!='      => $this->getProperty('id')

        ))
        ) {
            $this->modx->error->addField('name', $this->modx->lexicon('msoptionscolor_err_non_name_unique'));
        }

        $option = (int)$this->getProperty('option', $this->getProperty('option_'));
        $this->unsetProperty('option');

        // ral
        if ($this->modx->getOption('msoptionscolor_active_ral', null, false)) {
            $ral = $this->getProperty('ral');
            if (!empty($ral) && $msopRal = $this->modx->getObject('msopRal', array('ral' => $ral))) {
                $this->setProperty('color', $msopRal->get('html'));
            }
        }


        return parent::beforeSet();
    }
}

return 'msopColorUpdateProcessor';