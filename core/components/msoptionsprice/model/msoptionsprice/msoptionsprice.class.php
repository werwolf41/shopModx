<?php

//ini_set('display_errors', 1);
//ini_set('error_reporting', -1);

/**
 * The base class for msoptionsprice.
 */
class msoptionsprice
{
    /** @var modX $modx */
    public $modx;

    /** @var mixed|null $namespace */
    public $namespace = 'msoptionsprice';
    /** @var array $config */
    public $config = array();
    /** @var array $initialized */
    public $initialized = array();

    /** @var miniShop2 $miniShop2 */
    public $miniShop2;

    /**
     * @param modX  $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config = array())
    {
        $this->modx =& $modx;

        $corePath = $this->getOption('core_path', $config,
            $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/msoptionsprice/');
        $assetsPath = $this->getOption('assets_path', $config,
            $this->modx->getOption('assets_path', null, MODX_ASSETS_PATH) . 'components/msoptionsprice/');
        $assetsUrl = $this->getOption('assets_url', $config,
            $this->modx->getOption('assets_url', null, MODX_ASSETS_URL) . 'components/msoptionsprice/');
        $connectorUrl = $assetsUrl . 'connector.php';

        $this->config = array_merge(array(
            'namespace'       => $this->namespace,
            'connectorUrl'    => $connectorUrl,
            'assetsBasePath'  => MODX_ASSETS_PATH,
            'assetsBaseUrl'   => MODX_ASSETS_URL,
            'assetsPath'      => $assetsPath,
            'assetsUrl'       => $assetsUrl,
            'cssUrl'          => $assetsUrl . 'css/',
            'jsUrl'           => $assetsUrl . 'js/',
            'corePath'        => $corePath,
            'modelPath'       => $corePath . 'model/',
            'handlersPath'    => $corePath . 'handlers/',
            'processorsPath'  => $corePath . 'processors/',
            'templatesPath'   => $corePath . 'elements/templates/mgr/',
            'jsonResponse'    => true,
            'prepareResponse' => true,
            'showLog'         => false,
        ), $config);


        $this->modx->addPackage('msoptionsprice', $this->config['modelPath']);
        $this->modx->lexicon->load('msoptionsprice:default');
        $this->namespace = $this->getOption('namespace', $config, 'msoptionsprice');

        $this->miniShop2 = $modx->getService('miniShop2');
        if (!($this->miniShop2 instanceof miniShop2)) {
            return false;
        }

    }

    /**
     * @param       $n
     * @param array $p
     */
    public function __call($n, array$p)
    {
        echo __METHOD__ . ' says: ' . $n;
    }

    /**
     * @param       $key
     * @param array $config
     * @param null  $default
     *
     * @return mixed|null
     */
    public function getOption($key, $config = array(), $default = null, $skipEmpty = false)
    {
        $option = $default;
        if (!empty($key) AND is_string($key)) {
            if ($config != null AND array_key_exists($key, $config)) {
                $option = $config[$key];
            } elseif (array_key_exists($key, $this->config)) {
                $option = $this->config[$key];
            } elseif (array_key_exists("{$this->namespace}_{$key}", $this->modx->config)) {
                $option = $this->modx->getOption("{$this->namespace}_{$key}");
            }
        }
        if ($skipEmpty AND empty($option)) {
            $option = $default;
        }

        return $option;
    }

    public function initialize($ctx = 'web', array $scriptProperties = array())
    {
        if (isset($this->initialized[$ctx])) {
            return $this->initialized[$ctx];
        }

        $this->modx->error->reset();
        $this->config = array_merge($this->config, $scriptProperties, array('ctx' => $ctx));

        if ($ctx != 'mgr' AND (!defined('MODX_API_MODE') OR !MODX_API_MODE)) {

        }

        $initialize = true;
        $this->initialized[$ctx] = $initialize;

        return $initialize;
    }

    /**
     * @return string
     */
    public function getVersionMiniShop2()
    {
        return isset($this->miniShop2->version) ? $this->miniShop2->version : '2.2.0';
    }


    /**
     * @param array $properties
     */
    public function loadResourceJsCss(array $properties = array())
    {
        $properties = array_merge($this->config, $properties);
        $pls = $this->miniShop2->pdoTools->makePlaceholders($properties);

        $js = trim($this->getOption('frontendJs', $properties,
            $this->modx->getOption('msoptionsprice_frontendJs', null),
            true));
        $this->modx->regClientScript(str_replace($pls['pl'], $pls['vl'], $js));

        $css = trim($this->getOption('frontendCss', $properties,
            $this->modx->getOption('msoptionsprice_frontendCss', null),
            true));
        $this->modx->regClientCSS(str_replace($pls['pl'], $pls['vl'], $css));

        $action = trim($this->getOption('actionUrl', $properties,
            $this->modx->getOption('msoptionsprice_actionUrl', null),
            true));

        $config = array();
        $config['assetsBaseUrl'] = str_replace($pls['pl'], $pls['vl'], $properties['assetsBaseUrl']);
        $config['assetsUrl'] = str_replace($pls['pl'], $pls['vl'], $properties['assetsUrl']);
        $config['actionUrl'] = str_replace($pls['pl'], $pls['vl'], $action);
        $config['miniShop2']['version'] = $this->getVersionMiniShop2();
        $config['ctx'] = $this->modx->context->get('key');

        $config = json_encode($config, true);
        $this->modx->regClientStartupScript("<script type=\"text/javascript\">msOptionsPriceConfig={$config};</script>",
            true);

    }


    /**
     * return lexicon message if possibly
     *
     * @param string $message
     *
     * @return string $message
     */
    public function lexicon($message, $placeholders = array())
    {
        $key = '';
        if ($this->modx->lexicon->exists($message)) {
            $key = $message;
        } elseif ($this->modx->lexicon->exists($this->namespace . '_' . $message)) {
            $key = $this->namespace . '_' . $message;
        }
        if ($key !== '') {
            $message = $this->modx->lexicon->process($key, $placeholders);
        }

        return $message;
    }

    /**
     * @param string $message
     * @param array  $data
     * @param array  $placeholders
     *
     * @return array|string
     */
    public function failure($message = '', $data = array(), $placeholders = array())
    {
        $response = array(
            'success' => false,
            'message' => $this->lexicon($message, $placeholders),
            'data'    => $data,
        );

        return $this->config['jsonResponse'] ? $this->modx->toJSON($response) : $response;
    }

    /**
     * @param string $message
     * @param array  $data
     * @param array  $placeholders
     *
     * @return array|string
     */
    public function success($message = '', $data = array(), $placeholders = array())
    {
        $response = array(
            'success' => true,
            'message' => $this->lexicon($message, $placeholders),
            'data'    => $data,
        );

        return $this->config['jsonResponse'] ? $this->modx->toJSON($response) : $response;
    }


    /**
     * @param        $array
     * @param string $delimiter
     *
     * @return array
     */
    public function explodeAndClean($array, $delimiter = ',')
    {
        $array = explode($delimiter, $array);     // Explode fields to array
        $array = array_map('trim', $array);       // Trim array's values
        $array = array_keys(array_flip($array));  // Remove duplicate fields
        $array = array_filter($array);            // Remove empty values from array
        return $array;
    }

    /**
     * @param        $array
     * @param string $delimiter
     *
     * @return array|string
     */
    public function cleanAndImplode($array, $delimiter = ',')
    {
        $array = array_map('trim', $array);       // Trim array's values
        $array = array_keys(array_flip($array));  // Remove duplicate fields
        $array = array_filter($array);            // Remove empty values from array
        $array = implode($delimiter, $array);

        return $array;
    }

    /**
     * @param array  $array
     * @param string $prefix
     *
     * @return array
     */
    public function flattenArray(array $array = array(), $prefix = '')
    {
        $outArray = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $outArray = $outArray + $this->flattenArray($value, $prefix . $key . '.');
            } else {
                $outArray[$prefix . $key] = $value;
            }
        }

        return $outArray;
    }

    public function isWorkingClassKey(modResource $resource)
    {
        return in_array($resource->get('class_key'),
            $this->explodeAndClean($this->getOption('working_class_key', null, 'msProduct', true)));
    }

    public function isWorkingTemplates(modResource $resource)
    {
        return in_array($resource->get('template'),
            $this->explodeAndClean($this->getOption('working_templates', null)));
    }

    /**
     * @param string $action
     * @param array  $data
     *
     * @return array|modProcessorResponse|string
     */
    public function runProcessor($action = '', $data = array())
    {
        $this->modx->error->reset();
        $processorsPath = !empty($this->config['processorsPath']) ? $this->config['processorsPath'] : MODX_CORE_PATH;
        /* @var modProcessorResponse $response */
        $response = $this->modx->runProcessor($action, $data, array('processors_path' => $processorsPath));

        return $this->config['prepareResponse'] ? $this->prepareResponse($response) : $response;
    }

    /**
     * This method returns prepared response
     *
     * @param mixed $response
     *
     * @return array|string $response
     */
    public function prepareResponse($response)
    {
        if ($response instanceof modProcessorResponse) {
            $output = $response->getResponse();
        } else {
            $message = $response;
            if (empty($message)) {
                $message = $this->lexicon('err_unknown');
            }
            $output = $this->failure($message);
        }
        if ($this->config['jsonResponse'] AND is_array($output)) {
            $output = $this->modx->toJSON($output);
        } elseif (!$this->config['jsonResponse'] AND !is_array($output)) {
            $output = $this->modx->fromJSON($output);
        }

        return $output;
    }

    /** @return array Grid Option Fields */
    public function getGridOptionFields()
    {
        $fields = $this->getOption('grid_option_fields', null,
            'id,key,value', true);
        $fields .= ',id,key,value,properties,actions';
        $fields = $this->explodeAndClean($fields);

        return $fields;
    }

    /** @return array Grid Modification Fields */
    public function getGridModificationFields()
    {
        $fields = $this->getOption('grid_modification_fields', null,
            'id,type,price,article,weight,count,image', true);
        $fields .= ',id,type,price,rank,properties,actions';
        $fields = $this->explodeAndClean($fields);

        return $fields;
    }

    /**
     * @param modManagerController $controller
     * @param array                $setting
     */
    public function loadControllerJsCss(modManagerController &$controller, array $setting = array())
    {
        $controller->addLexiconTopic('msoptionsprice:default');

        $config = $this->config;
        foreach (array('resource', 'user') as $key) {
            if (isset($config[$key]) AND is_object($config[$key]) AND $config[$key] instanceof xPDOObject) {
                /** @var $config xPDOObject[] */
                $row = $config[$key]->toArray();
                unset($config[$key]);
                $config[$key] = $row;
            }
        }

        $config['connector_url'] = $this->config['connectorUrl'];
        $config['grid_option_fields'] = $this->getGridOptionFields();
        $config['grid_modification_fields'] = $this->getGridModificationFields();

        if (!empty($setting['css'])) {
            $controller->addCss($this->config['cssUrl'] . 'mgr/main.css');
            $controller->addCss($this->config['cssUrl'] . 'mgr/bootstrap.buttons.css');
        }

        if (!empty($setting['config'])) {
            $controller->addHtml("<script type='text/javascript'>msoptionsprice.config={$this->modx->toJSON($config)}</script>");
        }

        if (!empty($setting['tools'])) {
            $controller->addJavascript($this->config['jsUrl'] . 'mgr/msoptionsprice.js');
            $controller->addJavascript($this->config['jsUrl'] . 'mgr/misc/tools.js');
            $controller->addJavascript($this->config['jsUrl'] . 'mgr/misc/combo.js');
        }

        if (!empty($setting['modification'])) {
            $controller->addLastJavascript($this->config['jsUrl'] . 'mgr/modification/modification.window.js');
            $controller->addLastJavascript($this->config['jsUrl'] . 'mgr/modification/modification.grid.js');
        }

        if (!empty($setting['option'])) {
            $controller->addLastJavascript($this->config['jsUrl'] . 'mgr/option/option.window.js');
            $controller->addLastJavascript($this->config['jsUrl'] . 'mgr/option/option.grid.js');
        }

        if (!empty($setting['resource/inject'])) {
            $controller->addLastJavascript($this->config['jsUrl'] . 'mgr/resource/inject/inject.tab.js');
            $controller->addLastJavascript($this->config['jsUrl'] . 'mgr/resource/inject/inject.panel.js');
        }

    }

    public function getModificationById($id = 0, $rid = 0)
    {
        $class = 'msopModification';
        /** @var msopModification $modification */
        $modification = $this->modx->getObject($class, $id);

        /** @var $product msProduct */
        if (!$modification AND $rid AND $product = $this->modx->getObject('msProduct', (int)$rid)) {
            $modification = $this->modx->newObject($class);
            $modification->fromArray(array_merge($product->toArray(), array(
                // 'price'  => $product->getPrice(),
                // 'weight' => $product->getWeight(),
            )));
        }

        return $modification ? $modification->toArray() : null;
    }

    public function getModificationByImage(
        $rid = 0,
        $iid = 0,
        array $options = array(),
        $strict = null,
        $excludeIds = array(0),
        $excludeType = array(0)
    ) {
        /*******************************************/
        $response = $this->miniShop2->invokeEvent('msopOnBeforeGetModification', array(
            'rid'         => $rid,
            'iid'         => $iid,
            'options'     => $options,
            'excludeIds'  => $excludeIds,
            'excludeType' => $excludeType
        ));
        if (!$response['success']) {
            return $response['message'];
        }
        $rid = $response['data']['rid'];
        $iid = $response['data']['iid'];
        $options = $response['data']['options'];
        $excludeIds = $response['data']['excludeIds'];
        $excludeType = $response['data']['excludeType'];
        /*******************************************/

        /* exclude options */
        $excludeOptions = $this->getOption('exclude_modification_options', null, '', true);

        /* TODO */
        /*
         * сделать проверку на исключение опции цвета
         */
        $excludeOptions .= ',' . $this->getOption('modification_image_option', null, 'color', true);
        $excludeOptions = $this->explodeAndClean($excludeOptions);
        foreach ($excludeOptions as $excludeOption) {
            unset($options[$excludeOption]);
        }

        $class = 'msopModification';
        $classOption = 'msopModificationOption';

        $q = $this->modx->newQuery($class);
        $q->where(array(
            "{$class}.rid"         => $rid,
            "{$class}.image"       => $iid,
            "{$class}.id:NOT IN"   => $excludeIds,
            "{$class}.type:NOT IN" => $excludeType,
            "{$class}.active"      => true,
        ));

        $q->select(array(
            "{$class}.id",
        ));

        if (is_null($strict)) {
            $strict = $this->getOption('search_modification_by_image_strict', null, false, true);
        }

        if (!$strict) {

            $sbq = $sbq2 = $sql = $sql2 = array();
            foreach ($options as $key => $value) {

                $alias = $this->getAlias($key);
                /** @var $sbq xPDOQuery[] */
                $sbq[$alias] = $this->modx->newQuery($classOption);
                $sbq[$alias]->setClassAlias($alias);
                $sbq[$alias]->groupby("{$alias}.mid");
                $sbq[$alias]->select(array(
                    "{$alias}.mid",
                ));
                $sbq[$alias]->where(array(
                    "{$alias}.key"   => $key,
                    "{$alias}.value" => $value,
                ));
                $sbq[$alias]->prepare();
                $sql[$alias] = $sbq[$alias]->toSQL();

                if (!$strict) {
                    $alias2 = $this->getAlias($alias);

                    /** @var $sbq2 xPDOQuery[] */
                    $sbq2[$alias2] = $this->modx->newQuery($classOption);
                    $sbq2[$alias2]->setClassAlias($alias2);
                    $sbq2[$alias2]->groupby("{$alias2}.mid");
                    $sbq2[$alias2]->select(array(
                        "{$alias2}.mid",
                    ));
                    $sbq2[$alias2]->where(array(
                        "{$alias2}.key" => $key,
                    ));
                    $sbq2[$alias2]->prepare();
                    $sql2[$alias2] = $sbq2[$alias2]->toSQL();

                    $q->query['where'][] = new xPDOQueryCondition(array(
                        'sql'         => "(IF(" .
                            "(SELECT count(*) FROM ({$sql2[$alias2]}) as {$alias2} WHERE {$alias2}.mid = {$class}.id), " .
                            "EXISTS (SELECT NULL FROM ({$sql[$alias]}) as {$alias} WHERE {$alias}.mid = {$class}.id) ," .
                            "TRUE" .
                            "))",
                        'conjunction' => "AND",
                    ));
                }
            }
        }

        $modification = $this->modx->getObject($class, $q);

        /*******************************************/
        $response = $this->miniShop2->invokeEvent('msopOnAfterGetModification', array(
            'rid'          => $rid,
            'iid'          => $iid,
            'options'      => $options,
            'excludeIds'   => $excludeIds,
            'excludeType'  => $excludeType,
            'modification' => $modification
        ));
        if (!$response['success']) {
            return $response['message'];
        }

        /*******************************************/

        return $modification ? $modification->toArray() : null;
    }

    public function getAlias($key = '', $prefix = '_')
    {
        $alias = $prefix . str_replace(array('-', '/'), array(''), $key);

        return $alias;
    }

    public function getModificationByOptions(
        $rid = 0,
        array $options = array(),
        $strict = null,
        $excludeIds = array(0),
        $excludeType = array(0)
    ) {
        /*******************************************/
        $response = $this->miniShop2->invokeEvent('msopOnBeforeGetModification', array(
            'rid'         => $rid,
            'options'     => $options,
            'excludeIds'  => $excludeIds,
            'excludeType' => $excludeType
        ));
        if (!$response['success']) {
            return $response['message'];
        }
        $rid = $response['data']['rid'];
        $options = $response['data']['options'];
        $excludeIds = $response['data']['excludeIds'];
        $excludeType = $response['data']['excludeType'];
        /*******************************************/

        /* exclude options */
        $excludeOptions = $this->getOption('exclude_modification_options', null, '', true);
        $excludeOptions = $this->explodeAndClean($excludeOptions);
        foreach ($excludeOptions as $excludeOption) {
            unset($options[$excludeOption]);
        }

        $class = 'msopModification';
        $classOption = 'msopModificationOption';

        $q = $this->modx->newQuery($class);
        $q->where(array(
            "{$class}.rid"         => $rid,
            "{$class}.id:NOT IN"   => $excludeIds,
            "{$class}.type:NOT IN" => $excludeType,
            "{$class}.active"      => true,
        ));
        $q->select(array(
            "{$class}.id"
        ));

        if (empty($options)) {
            $q->andCondition(array(
                "{$class}.id:IN" => array(0),
            ));
        }

        if (is_null($strict)) {
            $strict = $this->getOption('search_modification_strict', null, false, true);
        }

        $sbq = $sbq2 = $sql = $sql2 = array();
        foreach ($options as $key => $value) {

            $alias = $this->getAlias($key);
            /** @var $sbq xPDOQuery[] */
            $sbq[$alias] = $this->modx->newQuery($classOption);
            $sbq[$alias]->setClassAlias($alias);
            $sbq[$alias]->groupby("{$alias}.mid");
            $sbq[$alias]->select(array(
                "{$alias}.mid",
            ));
            $sbq[$alias]->where(array(
                "{$alias}.key"   => $key,
                "{$alias}.value" => $value,
            ));
            $sbq[$alias]->prepare();
            $sql[$alias] = $sbq[$alias]->toSQL();

            if (!$strict) {
                $alias2 = $this->getAlias($alias);

                /** @var $sbq2 xPDOQuery[] */
                $sbq2[$alias2] = $this->modx->newQuery($classOption);
                $sbq2[$alias2]->setClassAlias($alias2);
                $sbq2[$alias2]->groupby("{$alias2}.mid");
                $sbq2[$alias2]->select(array(
                    "{$alias2}.mid",
                ));
                $sbq2[$alias2]->where(array(
                    "{$alias2}.key" => $key,
                ));
                $sbq2[$alias2]->prepare();
                $sql2[$alias2] = $sbq2[$alias2]->toSQL();

                $q->query['where'][] = new xPDOQueryCondition(array(
                    'sql'         => "(IF(" .
                        "(SELECT count(*) FROM ({$sql2[$alias2]}) as {$alias2} WHERE {$alias2}.mid = {$class}.id), " .
                        "EXISTS (SELECT NULL FROM ({$sql[$alias]}) as {$alias} WHERE {$alias}.mid = {$class}.id) ," .
                        "TRUE" .
                        "))",
                    'conjunction' => "AND",
                ));
            } else {
                $q->query['where'][] = new xPDOQueryCondition(array(
                    'sql'         => "EXISTS (SELECT NULL FROM ({$sql[$alias]}) as {$alias} WHERE {$alias}.mid = {$class}.id)",
                    'conjunction' => "AND",
                ));
            }
        }

        /*$s = $q->prepare();
        $sql = $q->toSQL();
        $this->modx->log(1, print_r($sql, 1));
        $s->execute();
        print_r($s->errorInfo());*/

        $modification = $this->modx->getObject($class, $q);
        /*******************************************/
        $response = $this->miniShop2->invokeEvent('msopOnAfterGetModification', array(
            'rid'          => $rid,
            'options'      => $options,
            'excludeIds'   => $excludeIds,
            'excludeType'  => $excludeType,
            'modification' => $modification
        ));
        if (!$response['success']) {
            return $response['message'];
        }

        /*******************************************/

        return $modification ? $modification->toArray() : null;
    }

    public function getCostByType($type = 0, $cost = 0, $price = 0)
    {
        if (preg_match('/%$/', $cost)) {
            $cost = str_replace('%', '', $cost);
            if (empty($cost)) {
                $cost = 1;
            }
            $cost = $price / 100 * $cost;
        }

        switch ($type) {
            case 1:
                break;
            case 2:
                $cost = $price + $cost;
                break;
            case 3:
                $cost = $price - $cost;
                break;
            default:
                break;
        }

        if ($cost < 0) {
            $cost = 0;
        }

        if (!$cost AND !$this->getOption('allow_zero_cost', null, false)) {
            $cost = $price;
        }

        return $cost;
    }

    public function getMassByType($type = 0, $mass = 0, $weight = 0)
    {
        if (preg_match('/%$/', $mass)) {
            $mass = str_replace('%', '', $mass);
            if (empty($mass)) {
                $mass = 1;
            }
            $mass = $weight / 100 * $mass;
        }

        switch ($type) {
            case 1:
                break;
            case 2:
                $mass = $weight + $mass;
                break;
            case 3:
                $mass = $weight - $mass;
                break;
            default:
                break;
        }

        if ($mass < 0) {
            $mass = 0;
        }

        if (!$mass AND !$this->getOption('allow_zero_mass', null, false)) {
            $mass = $weight;
        }

        return $mass;
    }


    public function getCostByModification($rid = 0, $price = 0, $modification = array(), $isAjax = false)
    {
        if (!$modification) {
            $modification = array();
        }

        /*******************************************/
        $response = $this->miniShop2->invokeEvent('msopOnBeforeGetCost', array(
            'rid'          => $rid,
            'price'        => $price,
            'modification' => $modification,
            'isAjax'       => $isAjax
        ));
        if (!$response['success']) {
            return $response['message'];
        }
        $rid = $response['data']['rid'];
        $price = $response['data']['price'];
        $modification = $response['data']['modification'];
        /*******************************************/

        $type = $this->modx->getOption('type', $modification, 0, true);
        $cost = $this->modx->getOption('price', $modification, 0, true);

        $cost = $this->getCostByType($type, $cost, $price);
        $cost = $this->formatPrice($cost, !$isAjax, false);

        /*******************************************/
        $response = $this->miniShop2->invokeEvent('msopOnAfterGetCost', array(
            'rid'          => $rid,
            'cost'         => $cost,
            'modification' => $modification,
            'isAjax'       => $isAjax
        ));
        if (!$response['success']) {
            return $response['message'];
        }
        $cost = $response['data']['cost'];

        /*******************************************/

        return $cost;
    }

    public function getMassByModification($rid = 0, $weight = 0, $modification = array(), $isAjax = false)
    {
        if (!$modification) {
            $modification = array();
        }

        /*******************************************/
        $response = $this->miniShop2->invokeEvent('msopOnBeforeGetMass', array(
            'rid'          => $rid,
            'weight'       => $weight,
            'modification' => $modification,
            'isAjax'       => $isAjax
        ));
        if (!$response['success']) {
            return $response['message'];
        }
        $rid = $response['data']['rid'];
        $weight = $response['data']['weight'];
        $modification = $response['data']['modification'];
        /*******************************************/

        $type = $this->modx->getOption('type', $modification, 0, true);
        $mass = $this->modx->getOption('weight', $modification, 0, true);

        $mass = $this->getMassByType($type, $mass, $weight);
        $mass = $this->formatWeight($mass, !$isAjax, false);

        /*******************************************/
        $response = $this->miniShop2->invokeEvent('msopOnAfterGetMass', array(
            'rid'          => $rid,
            'mass'         => $mass,
            'modification' => $modification,
            'isAjax'       => $isAjax
        ));
        if (!$response['success']) {
            return $response['message'];
        }
        $mass = $response['data']['mass'];

        /*******************************************/

        return $mass;
    }


    public function setProductOptions($rid = 0, array $values = array())
    {
        $options = array();
        /** @var $product msProduct */
        if (!$product = $this->modx->getObject('msProduct', $rid)) {
            return $options;
        }
        $options = $product->loadData()->get('options');

        foreach ($values as $k => $v) {
            if (!is_array($v)) {
                $v = array($v);
            }
            if (isset($options[$k])) {
                $options[$k] = array_merge($options[$k], $v);
            } else {
                $options[$k] = $v;
            }
        }

        foreach ($options as $k => $v) {
            if (is_array($v)) {
                $options[$k] = array_map('trim', $options[$k]);
                $options[$k] = array_keys(array_flip($options[$k]));
                $options[$k] = array_diff($options[$k], array(''));
                sort($options[$k]);
            } else {
                $options[$k] = trim($options[$k]);
            }
            $product->set($k, $options[$k]);
        }
        $product->set('options', $options);
        $product->save();

        $options = $this->modx->call('msopModificationOption', 'getProductOptions', array(&$this->modx, $rid));

        return $options;
    }


    public function removeProductOptions($rid = 0, array $values = array())
    {
        $options = array();
        /** @var $product msProduct */
        if (!$product = $this->modx->getObject('msProduct', $rid)) {
            return $options;
        }
        $options = $product->loadData()->get('options');

        foreach ($values as $k => $v) {
            if (!isset($options[$k])) {
                continue;
            }
            if (!is_array($v)) {
                $v = array($v);
            }
            $options[$k] = array_diff($options[$k], $v);
        }

        foreach ($options as $k => $v) {
            if (is_array($v)) {
                $options[$k] = array_map('trim', $options[$k]);
                $options[$k] = array_keys(array_flip($options[$k]));
                $options[$k] = array_diff($options[$k], array(''));
                sort($options[$k]);
            } else {
                $options[$k] = trim($options[$k]);
            }
            $product->set($k, $options[$k]);
        }
        $product->set('options', $options);
        $product->save();

        $options = $this->modx->call('msopModificationOption', 'getProductOptions', array(&$this->modx, $rid));

        return $options;
    }
    
    /**
     * @param int $number
     *
     * @return float
     */
    public function formatNumber($number = 0, $ceil = false)
    {
        $number = str_replace(',', '.', $number);
        $number = (float)$number;

        if ($ceil) {
            $number = ceil($number / 10) * 10;
        }

        return round($number, 3);
    }


    /**
     * @param string $price
     * @param bool   $number
     *
     * @return float|mixed|string
     */
    public function formatPrice($price = '0', $number = false, $ceil = false)
    {
        $price = $this->formatNumber($price, $ceil);
        $pf = $this->modx->fromJSON($this->getOption('number_format', null, '[0, 1]', true));
        if (is_array($pf)) {
            $price = round($price, $pf[0], $pf[1]);
        }

        if (!$number) {
            $pf = $this->modx->fromJSON($this->modx->getOption('ms2_price_format', null, '[2, ".", " "]', true));
            if (is_array($pf)) {
                $price = number_format($price, $pf[0], $pf[1], $pf[2]);
            }

            if ($this->modx->getOption('ms2_price_format_no_zeros', null, false, true)) {
                if (preg_match('/\..*$/', $price, $matches)) {
                    $tmp = rtrim($matches[0], '.0');
                    $price = str_replace($matches[0], $tmp, $price);
                }
            }
        }

        return $price;
    }

    public function formatWeight($weight = '0', $number = false, $ceil = false)
    {
        $weight = $this->formatNumber($weight, $ceil);
        $pf = $this->modx->fromJSON($this->getOption('number_format', null, '[0, 1]', true));
        if (is_array($pf)) {
            $weight = round($weight, $pf[0], $pf[1]);
        }

        if (!$number) {
            $pf = $this->modx->fromJSON($this->modx->getOption('ms2_weight_format', null, '[3, ".", " "]', true));
            if (is_array($pf)) {
                $weight = number_format($weight, $pf[0], $pf[1], $pf[2]);
            }

            if ($this->modx->getOption('ms2_weight_format_no_zeros', null, false, true)) {
                if (preg_match('/\..*$/', $weight, $matches)) {
                    $tmp = rtrim($matches[0], '.0');
                    $weight = str_replace($matches[0], $tmp, $weight);
                }
            }
        }

        return $weight;
    }

}