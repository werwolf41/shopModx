<?php

class msopRalUpdateProcessor extends modObjectUpdateProcessor
{
    public $classKey = 'msopRal';
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
        /*		if ($this->modx->getObject('msopRal',array('name' => $this->getProperty('name'), 'id:!=' => $this->getProperty('id') ))) {
                    $this->modx->error->addField('name', $this->modx->lexicon('msoptionscolor_err_non_name_unique'));
                }*/
        if ($this->modx->getObject('msopRal',
            array('ral' => $this->getProperty('ral'), 'id:!=' => $this->getProperty('id')))
        ) {
            $this->modx->error->addField('ral', $this->modx->lexicon('msoptionscolor_err_non_key_unique'));
        }

        return parent::beforeSet();
    }
}

return 'msopRalUpdateProcessor';