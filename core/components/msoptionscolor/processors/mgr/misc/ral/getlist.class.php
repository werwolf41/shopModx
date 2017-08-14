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
     * {@inheritDoc}
     * @return mixed
     */
    public function process()
    {
        $beforeQuery = $this->beforeQuery();
        if ($beforeQuery !== true) {
            return $this->failure($beforeQuery);
        }
        $data = $this->getData();

        return $this->outputArray($data['results'], $data['total']);
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

        $data['results'] = array();
        if ($c->prepare() && $c->stmt->execute()) {
            while ($row = $c->stmt->fetch(PDO::FETCH_ASSOC)) {
                $data['results'][] = $this->prepareArray($row);
            }
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR, print_r($c->stmt->errorInfo(), true));
        }

        return $data;
    }

    /**
     * @param xPDOQuery $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $query = $this->getProperty('query');
        if (!empty($query)) {
            $c->where(array(
                "{$this->classKey}.ral:LIKE"            => "%{$query}%",
                "OR:{$this->classKey}.html:LIKE"        => "%{$query}%",
                "OR:{$this->classKey}.description:LIKE" => "%{$query}%",
                "OR:{$this->classKey}.name:LIKE"        => "%{$query}%"
            ));
            $c->where(array('active' => 1));
        }


        return $c;
    }

    /** {@inheritDoc} */
    public function prepareArray(array $row)
    {

        $key = $this->getProperty('key');
        if (!empty($key) AND isset($row[$key])) {
            $row['value'] = $row[$key];
        }
       // $row['value'] = $row['ral'];

        return $row;
    }


}

return 'msopRalGetListProcessor';