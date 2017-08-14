<?php

class msopColorActiveProcessor extends modObjectProcessor
{
    public $classKey = 'msopColor';

    /** {@inheritDoc} */
    public function process()
    {

        $id = $this->getProperty('id', null);
        if (empty($id)) {
            return $this->success();
        }
        $value = $this->getProperty('value', 0);

        if ($price = $this->modx->getObject('msopColor', $id)) {
            $price->set('active', $value);
            $price->save();
        }

        return $this->success();
    }

}

return 'msopColorActiveProcessor';