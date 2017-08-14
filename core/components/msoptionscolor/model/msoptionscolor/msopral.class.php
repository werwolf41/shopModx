<?php

class msopRal extends xPDOSimpleObject
{

    /** {@inheritdoc} */
    public function save($cacheFlag = null)
    {
        if ($html = $this->get('html')) {
            $this->set('html', strtolower($html));
        }

        return parent::save();
    }

}