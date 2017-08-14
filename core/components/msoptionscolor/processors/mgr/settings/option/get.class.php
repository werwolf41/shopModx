<?php

class msopOptionGetProcessor extends modObjectGetProcessor
{
    public $classKey = 'msopOption';
    public $languageTopics = array('msoptionscolor:default', 'msoptionscolor:manager');
    public $permission = 'msoptionscolorsetting_view';

    /** {@inheritDoc} */
    public function initialize()
    {
        if (!$this->modx->hasPermission($this->permission)) {
            return $this->modx->lexicon('access_denied');
        }

        return parent::initialize();
    }

    /** {@inheritDoc} */
    public function cleanup()
    {
        $array = $this->object->toArray();

        return $this->success('', $array);
    }
}

return 'msopOptionGetProcessor';