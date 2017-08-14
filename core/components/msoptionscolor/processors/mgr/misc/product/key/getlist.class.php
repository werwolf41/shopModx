<?php

class msoptionscolorProductOptionGetListProcessor extends modObjectCreateProcessor
{
    public $classKey = 'msopOption';
    public $languageTopics = array('msoptionscolor:default', 'msoptionscolor:manager');
    public $permission = 'msoptionscolorsetting_list';

    public $resource;

    /** {@inheritDoc} */
    public function initialize()
    {
        if (!$this->modx->hasPermission($this->permission)) {
            return $this->modx->lexicon('access_denied');
        }

        return parent::initialize();
    }

    public function process()
    {

        $start = $this->getProperty('start', 0);
        $limit = $this->getProperty('limit', 10);

        $fields = $this->prepareFields();
        $ret = array();
        //$ret[] = array('id' => 0, 'name' => 'Все');
        foreach ($fields as $i => $field) {
            $ret[] = array('id' => $field, 'name' => $field);
        }

        if (!empty($limit)) {
            for ($x = $start; $x < $start + $limit; $x++) {
                $_ret[] = $ret[$x];
            }
            foreach ($_ret as $k => $v) {
                if (is_null($v)) {
                    unset($_ret[$k]);
                }
            }

            return $this->outputArray($_ret, count($fields));
        }

        return $this->outputArray($ret, count($fields));
    }

    function prepareFields()
    {
        $query = $this->getProperty('query', '');

        $data = array_keys($this->modx->getFieldMeta('msProductData'));

        $excludeFields = array_map('trim', explode(',',
            $this->modx->getOption('msoptionscolor_exclude_product_property', null,
                'vendor,made_in,new,popular,favorite', true)));
        $excludeFields = array_values(array_unique(array_merge($excludeFields,
            array('id', 'article', 'price', 'old_price', 'weight', 'image', 'thumb', 'source'))));

        foreach ($data as $k => $v) {
            if (in_array($v, $excludeFields)) {
                unset($data[$k]);
            }
            if ($query !== '') {
                if (!(strpos($v, $query) === false) ? false : true) {
                    unset($data[$k]);
                }
            }
        }

        return array_values($data);
    }

}

return 'msoptionscolorProductOptionGetListProcessor';