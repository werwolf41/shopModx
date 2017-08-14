<?php

class msopColorGetProcessor extends modObjectGetProcessor
{
    public $classKey = 'msopColor';
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

        if ($msopRal = $this->object->getOne('Ral')) {
            $array['ral'] = $msopRal->get('ral');
        }

        //$array['option_'] = $array['option'];

        return $this->success('', $array);
    }
}

return 'msopColorGetProcessor';