<?php

class msopColor extends xPDOSimpleObject
{

    /** {@inheritdoc} */
    public function save($cacheFlag = null)
    {
        if ($color = $this->get('color')) {
            $this->set('color', strtolower($color));
        }

        return parent::save();
    }

}