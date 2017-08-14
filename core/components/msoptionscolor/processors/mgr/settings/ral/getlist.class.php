<?php

class msopRalGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'msopRal';
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
        if ($query = $this->getProperty('query')) {
            $c->where(array(
                'name:LIKE'    => '%' . $query . '%',
                'OR:ral:LIKE'  => '%' . $query . '%',
                'OR:html:LIKE' => '%' . $query . '%'
            ));
        }

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

}

return 'msopRalGetListProcessor';