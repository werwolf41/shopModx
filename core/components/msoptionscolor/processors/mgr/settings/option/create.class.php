<?php

class msopOptionCreateProcessor extends modObjectCreateProcessor
{
    public $classKey = 'msopOption';
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
        if ($this->modx->getObject('msopOption', array(
            'key' => $this->getProperty('key'),
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
            'rank'   => $this->modx->getCount('msopOption'),
            'active' => true
        ));

        return parent::beforeSave();
    }
}

return 'msopOptionCreateProcessor';