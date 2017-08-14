<?php

class msopOptionUpdateProcessor extends modObjectUpdateProcessor
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
        if ($this->modx->getObject('msopOption',
            array('name' => $this->getProperty('name'), 'id:!=' => $this->getProperty('id')))
        ) {
            $this->modx->error->addField('name', $this->modx->lexicon('msoptionscolor_err_non_name_unique'));
        }
        if ($this->modx->getObject('msopOption',
            array('key' => $this->getProperty('key'), 'id:!=' => $this->getProperty('id')))
        ) {
            $this->modx->error->addField('key', $this->modx->lexicon('msoptionscolor_err_non_key_unique'));
        }

        return parent::beforeSet();
    }
}

return 'msopOptionUpdateProcessor';