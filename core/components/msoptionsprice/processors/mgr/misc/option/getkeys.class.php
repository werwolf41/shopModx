<?php

/**
 * Get a list of msProductOption
 */
class msProductOptionGetKeysProcessor extends modObjectGetListProcessor
{
    public $objectType = 'msProductOption';
    public $classKey = 'msProductOption';
    public $defaultSortField = 'key';
    public $defaultSortDirection = 'ASC';
    public $languageTopics = array('default');
    public $permission = '';

    //msProductOption

    /**
     * @param xPDOQuery $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $classMsOption = 'msOption';
        $classMsCategoryOption = 'msCategoryOption';
        $classMsCategoryMember = 'msCategoryMember';

        $c->leftJoin($classMsOption, $classMsOption, "{$this->classKey}.key = {$classMsOption}.key");
        $c->select($this->modx->getSelectColumns($classMsOption, $classMsOption, '', array('caption'), false));

        if ($rid = (int)$this->getProperty('rid')) {
            $categories = array();
            $q = $this->modx->newQuery($classMsCategoryMember, array('product_id' => $rid));
            $q->select('category_id');
            if ($q->prepare() AND $q->stmt->execute()) {
                $categories = (array)$q->stmt->fetchAll(PDO::FETCH_COLUMN);
            }
            /** @var $product msProduct */
            if ($product = $this->modx->getObject('msProduct', $rid)) {
                $categories[] = $product->get('parent');
            }
            $categories = array_unique($categories);

            $c->leftJoin($classMsCategoryOption, $classMsCategoryOption,
                "{$classMsCategoryOption}.option_id = {$classMsOption}.id");
            $c->where(array(
                "{$classMsCategoryOption}.active"         => true,
                "{$classMsCategoryOption}.category_id:IN" => $categories,
            ));
            $c->orCondition(array(
                "{$classMsCategoryOption}.active" => null,
            ));
        }

        $c->groupby("{$this->classKey}.key");
        if ($query = trim($this->getProperty('query'))) {
            $c->where(array(
                "{$this->classKey}.key:LIKE" => "%{$query}%",
            ));
        }

        return $c;
    }

    /** {@inheritDoc} */
    public function outputArray(array $array, $count = false)
    {
        if ($this->getProperty('addall')) {
            $array = array_merge_recursive(array(
                array(
                    'id'   => 0,
                    'name' => $this->modx->lexicon('msoptionsprice_all')
                )
            ), $array);
        }
        if ($this->getProperty('novalue')) {
            $array = array_merge_recursive(array(
                array(
                    'id'   => 0,
                    'name' => $this->modx->lexicon('msoptionsprice_no')
                )
            ), $array);
        }

        return parent::outputArray($array, $count);
    }


    /**
     * @param xPDOObject $object
     *
     * @return array
     */
    public function prepareArray(array $array)
    {

        return $array;
    }

    /**
     * Get the data of the query
     * @return array
     */
    public function getData()
    {
        $data = array();
        $limit = intval($this->getProperty('limit'));
        $start = intval($this->getProperty('start'));

        $c = $this->modx->newQuery($this->classKey);
        $c = $this->prepareQueryBeforeCount($c);
        $data['total'] = $this->modx->getCount($this->classKey, $c);
        $c = $this->prepareQueryAfterCount($c);
        $c->select($this->modx->getSelectColumns($this->classKey, $this->classKey));

        $sortClassKey = $this->getSortClassKey();
        $sortKey = $this->modx->getSelectColumns($sortClassKey, $this->getProperty('sortAlias', $sortClassKey), '',
            array($this->getProperty('sort')));
        if (empty($sortKey)) {
            $sortKey = $this->getProperty('sort');
        }
        $c->sortby($sortKey, $this->getProperty('dir'));
        if ($limit > 0) {
            $c->limit($limit, $start);
        }

        if ($c->prepare() AND $c->stmt->execute()) {
            $data['results'] = $c->stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $data;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function iterate(array $data)
    {
        $list = array();
        $list = $this->beforeIteration($list);
        $this->currentIndex = 0;
        /** @var xPDOObject|modAccessibleObject $object */
        foreach ($data['results'] as $array) {
            $list[] = $this->prepareArray($array);
            $this->currentIndex++;
        }
        $list = $this->afterIteration($list);

        return $list;
    }

}

return 'msProductOptionGetKeysProcessor';