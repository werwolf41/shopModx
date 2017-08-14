<?php

class msProductGetOptionsProcessor extends modObjectGetListProcessor
{
    public $classKey = 'msProductOption';
    public $defaultSortField = 'product_id';

    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        if ($Option = $this->modx->getObject('msopOption', $this->getProperty('option_id'))) {
            $key = $Option->get('key');
        }
        $product_id = explode(',', $this->getProperty('product_id'));

        $c->select($this->modx->getSelectColumns('msProductOption', 'msProductOption'));
        $c->select(array('msProductOption.value as value', 'COUNT(msProductOption.product_id) AS total'));
        $c->where(array(
            'key'           => $key,
            'product_id:IN' => $product_id
        ));
        $c->groupby('value');

        return $c;
    }

    public function prepareRow(xPDOObject $object)
    {
        $array = $object->toArray();

        return $array;
    }

}

return 'msProductGetOptionsProcessor';