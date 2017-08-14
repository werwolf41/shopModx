<?php

class msopRalCreateProcessor extends modObjectCreateProcessor
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
        if ($this->modx->getObject('msopRal', array(
            'ral' => $this->getProperty('ral'),
        ))
        ) {
            return $this->modx->lexicon('msoptionscolor_err_non_key_unique');
        }

        return !$this->hasErrors();
    }

    /** {@inheritDoc} */
    public function beforeSave()
    {
        $this->object->fromArray(array(
            'rank'   => $this->modx->getCount('msopRal'),
            'active' => true
        ));

        return parent::beforeSave();
    }
}

return 'msopRalCreateProcessor';