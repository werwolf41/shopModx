<?php

class msopColorRemoveProcessor extends modObjectRemoveProcessor
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
}

return 'msopColorRemoveProcessor';