<?php

class msopOptionGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'msopOption';
    public $defaultSortField = 'rank';
    public $defaultSortDirection = 'asc';
    public $languageTopics = array('msoptionscolor:default', 'msoptionscolor:manager');
    public $permission = 'msoptionscolorsetting_list';

    /** {@inheritDoc} */
    public function initialize()
    {
        if (!$this->modx->hasPermission($this->permission)) {
            return $this->modx->lexicon('access_denied');
        }

        return parent::initialize();
    }

    /**
     * @param xPDOQuery $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $c->where(array('active' => 1));

        return $c;
    }

    /**
     * @param xPDOObject $object
     *
     * @return array
     */
    public function prepareRow(xPDOObject $object)
    {
        $array = $object->toArray();

        return $array;
    }

    /** {@inheritDoc} */
    public function outputArray(array $array, $count = false)
    {
        if ($this->getProperty('addall')) {
            $array = array_merge_recursive(array(
                array(
                    'id'   => 0,
                    'name' => $this->modx->lexicon('msoptionscolor_all'),
                    'key'  => 'all'
                )
            ), $array);
        }

        return parent::outputArray($array, $count);
    }

}

return 'msopOptionGetListProcessor';