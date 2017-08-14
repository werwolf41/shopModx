<?php

/**
 * The base class for msnewprice.
 */
class msnewprice
{
    /* @var modX $modx */
    public $modx;
    public $namespace = 'msnewprice';
    public $cache = null;
    public $config = array();
    public $active = false;
    public $options;
    public $authenticated = false;

    /* @var pdoTools $pdoTools */
    public $pdoTools;
    public $ms2;

    /**
     * @param modX  $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config = array())
    {
        $this->modx =& $modx;

        $this->namespace = $this->getOption('namespace', $config, 'msnewprice');
        $corePath = $this->modx->getOption('msnewprice_core_path', $config,
            $this->modx->getOption('core_path') . 'components/msnewprice/');
        $assetsUrl = $this->modx->getOption('msnewprice_assets_url', $config,
            $this->modx->getOption('assets_url') . 'components/msnewprice/');
        $connectorUrl = $assetsUrl . 'connector.php';

        $this->config = array_merge(array(
            'assetsUrl'    => $assetsUrl,
            'cssUrl'       => $assetsUrl . 'css/',
            'jsUrl'        => $assetsUrl . 'js/',
            'imagesUrl'    => $assetsUrl . 'images/',
            'connectorUrl' => $connectorUrl,

            'corePath'       => $corePath,
            'modelPath'      => $corePath . 'model/',
            'chunksPath'     => $corePath . 'elements/chunks/',
            'templatesPath'  => $corePath . 'elements/templates/',
            'chunkSuffix'    => '.chunk.tpl',
            'snippetsPath'   => $corePath . 'elements/snippets/',
            'processorsPath' => $corePath . 'processors/',

            'ctx'           => 'web',
            'json_response' => 0,
            'allowEmails'   => $this->modx->getOption('msnewprice_allowemails', $config, false),
            'frontend_css'  => $this->modx->getOption('msnewprice_front_css', null,
                '[[+assetsUrl]]css/web/default.css'),
            'frontend_js'   => $this->modx->getOption('msnewprice_front_js', null, '[[+assetsUrl]]js/web/default.js'),
            'webconnector'  => $assetsUrl . 'web-connector.php',

        ), $config);

        $this->modx->addPackage('msnewprice', $this->config['modelPath']);
        $this->modx->lexicon->load('msnewprice:default');
        $this->active = $this->modx->getOption('msnewprice_active', $config, false);
        $this->authenticated = $this->modx->user->isAuthenticated($this->modx->context->get('key'));

        if (!$this->ms2 = $modx->getService('miniShop2')) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'msOptionsPrice2 requires installed miniShop2.');

            return false;
        }

    }

    public function getOption($key, $config = array(), $default = null)
    {
        $option = $default;
        if (!empty($key) && is_string($key)) {
            if ($config != null && array_key_exists($key, $config)) {
                $option = $config[$key];
            } elseif (array_key_exists($key, $this->config)) {
                $option = $this->config[$key];
            } elseif (array_key_exists("{$this->namespace}.{$key}", $this->modx->config)) {
                $option = $this->modx->getOption("{$this->namespace}.{$key}");
            }
        }

        return $option;
    }

    public function initialize($ctx = 'web', $scriptProperties = array())
    {
        $this->config = array_merge($this->config, $scriptProperties);
        if (!$this->pdoTools) {
            $this->loadPdoTools();
        }
        $this->pdoTools->setConfig($this->config);
        $this->config['ctx'] = $ctx;
        if (!empty($this->initialized[$ctx])) {
            return true;
        }
        switch ($ctx) {
            case 'mgr':
                break;
            default:
                if (!defined('MODX_API_MODE') || !MODX_API_MODE) {
                    if ($css = trim($this->config['frontend_css'])) {
                        if (preg_match('/\.css/i', $css)) {
                            $this->modx->regClientCSS(str_replace('[[+assetsUrl]]', $this->config['assetsUrl'], $css));
                        }
                    }
                    if ($js = trim($this->config['frontend_js'])) {
                        if (!empty($js) && preg_match('/\.js/i', $js)) {
                            $this->modx->regClientScript(str_replace('[[+assetsUrl]]', $this->config['assetsUrl'],
                                $js));
                        }
                    }
                }
                $this->initialized[$ctx] = true;
                break;
        }

        return true;
    }

    public function onDocFormPrerender($sp)
    {
        $mode = $this->modx->getOption('mode', $sp);
        $this->modx->controller->addLexiconTopic('msnewprice:default');
        if ($mode != 'upd') {
            return;
        }

        $data = array();
        if ($msnewpricedata = $this->modx->getObject('msnewpricedata', array('msn_product_id' => $sp['id']))) {
           $data = $msnewpricedata->toArray();
        }

        $minishop2Version = isset($this->ms2->version) ? $this->ms2->version : '2.2.0';

        $data_js = preg_replace(array('/^\n/', '/\t{6}/'), '', '
			msnewprice = {};
			msnewprice.data = ' . $this->modx->toJSON($data) . ';
			msnewprice.minishop2 = {};
			msnewprice.minishop2.version = "' . $minishop2Version . '";
		');

        $this->modx->regClientStartupScript("<script type=\"text/javascript\">\n" . $data_js . "\n</script>", true);
        $this->modx->regClientStartupScript($this->getOption('jsUrl') . 'mgr/misc/zeroclipboard/ZeroClipboard.min.js');
        $res = $this->modx->getObject('modResource', $sp['id']);
        if ($res && ($res->get('class_key') == 'msProduct')) {
            $this->modx->regClientStartupScript($this->getOption('jsUrl') . 'mgr/misc/zeroclipboard/ZeroClipboard.min.js');
            $this->modx->regClientStartupScript($this->getOption('jsUrl') . 'mgr/inject/tab.js');
        }
    }

    public function OnBeforeDocFormSave($sp)
    {
        $mode = $this->modx->getOption('mode', $sp);
        if ($mode == 'upd') {
            $res = $this->modx->getObject('modResource', $sp['id']);
            if ($res->get('class_key') == 'msProduct') {
                $this->config['json_response'] = 1;
                $id = (int)$_POST['id'];
                $msn = $_POST['msn'];
                $newprice = $msn['newprice'];
                $startdate = $msn['startdate'];
                $stopdate = $msn['stopdate'];
                $description = $msn['description'];
                $action = $msn['action'];
                $overwrite = $msn['overwrite'];
                if (!empty($action) || !empty($overwrite)) {
                    // если чекбокс акционный или перезаписать то работаем
                    $startdate_ = preg_match('/^\d+$/', $startdate) ? $startdate : strtotime($startdate);
                    $stopdate_ = preg_match('/^\d+$/', $stopdate) ? $stopdate : strtotime($stopdate);
                    $current = time();
                    $delta = $stopdate_ - $startdate_;
                    // проверка времени
                    // если дата окончания меньше даты начала то ошибка
                    // если дата окончания меньше текущей даты то ошибка
                    if (($delta < 0) || ($current > $stopdate_)) {
                        echo $this->error('msnewprice_err_not_date');
                        exit;
                    }
                    if (!$data = $this->modx->getObject('msnewpricedata', array('msn_product_id' => $id))) {
                        $data = $this->modx->newObject('msnewpricedata');
                        $data->set('msn_product_id', $id);
                    }
                    $data->fromArray(array(
                        'msn_newprice'    => $newprice,
                        'msn_action'      => $action,
                        'msn_overwrite'   => $overwrite,
                        'msn_description' => $description,
                        'msn_startdate'   => $startdate,
                        'msn_stopdate'    => $stopdate,
                    ));
                    if ($data->save()) {
                        // вызываем событие
                        if ($overwrite) {
                            $response = $this->invokeEvent('OnMsNewPriceOverwriteSet', array(
                                'newprice'   => $data,
                                'product_id' => $id,
                            ));
                            if (!$response['success']) {
                                return $response['message'];
                            }
                        }
                        if ($action) {
                            $response = $this->invokeEvent('OnMsNewPriceStockSet', array(
                                'newprice'   => $data,
                                'product_id' => $id,
                            ));
                            if (!$response['success']) {
                                return $response['message'];
                            }
                        }
                    }
                } elseif (empty($action) && empty($overwrite)) {
                    // если чекбоксы сняты удаляем запись если она есть
                    if ($data = $this->modx->getObject('msnewpricedata', array('msn_product_id' => $id))) {
                        $data->remove();
                    }
                }
            }
        }
    }

    public function msOnBeforeAddToCart($sp, $product = 0)
    {
        $id = $product->get('id');
        if ($data = $this->modx->getObject('msnewpricedata', array('msn_product_id' => $id))) {
            if ($data->get('msn_action')) {
                $product->set('price', $data->get('msn_newprice'));
            }
        }
    }

    public function OnWebPageInit($sp)
    {
        if (empty($_REQUEST['msnewprice_action']) || empty($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
            return;
        }
        $ac = trim(strtolower($_REQUEST['msnewprice_action']));
        $user_id = $this->modx->user->id;
        $res_id = $_REQUEST['resource'];
        $list = !empty($_REQUEST['list'])
            ? (string)$_REQUEST['list']
            : 'default';
        switch ($ac) {
            case 'add':
            case 'remove': {
                if ($user_id == 0) {
                    $response = $this->error('msnewprice_err_no_user');
                } elseif (!$this->WorkOrNot($res_id)) {
                    $response = $this->error('msnewprice_err_add_resource');
                } else {
                    $res = $this->modx->getObject('modResource', array('id' => $res_id));
                    $arr = array(
                        'user_id' => $user_id,
                        'res_id'  => $res_id,
                        'list'    => $list,
                        'hash'    => md5($user_id . $res_id . $list)
                    );
                    if ($ac == 'add') {
                        if (!$count = $this->modx->getCount('msnewpricelist', $arr)) {
                            $msnitem = $this->modx->newObject('msnewpricelist', $arr);
                            if ($msnitem->save()) {
                                $response = $this->success('msnewprice_add_resource');
                            }
                        } else {
                            $response = $this->error('msnewprice_err_add_resource');
                        }
                    } else {
                        if ($msnitem = $this->modx->getObject('msnewpricelist', $arr)) {
                            $msnitem->remove();
                        }
                        $response = $this->success('msnewprice_remove_resource');
                    }
                }
                break;
            }
            default:
                break;
        }
        echo $this->modx->toJSON($response);
        @session_write_close();
        exit;

    }

    public function OnSiteSettingsRender($sp)
    {
        $this->modx->controller->addLexiconTopic('msfavorites:default');
        $this->modx->regClientStartupScript($this->getOption('jsUrl') . 'mgr/misc/msn.combo.js');

        return '';
    }

    public function OnBeforeEmptyTrash($sp)
    {
        $deletedids = $this->modx->event->params['ids'];
        if (!empty($deletedids)) {
            $this->modx->removeCollection('msnewpricedata', array(
                'msn_product_id:IN' => $deletedids,
            ));
            $this->modx->removeCollection('msnewpricelist', array(
                'res_id:IN' => $deletedids,
            ));
        }
    }

    public function WorkOrNot($res_id)
    {
        if (empty($res_id) || !$this->modx->getCount('modResource',
                array('id' => $res_id, 'published' => 1, 'deleted' => 0))
        ) {
            return false;
        }

        return true;
    }

    public function error($message = '', $data = array(), $placeholders = array())
    {
        $response = array(
            'success' => false,
            'message' => $this->modx->lexicon($message, $placeholders),
            'data'    => $data,
        );

        return $this->config['json_response']
            ? $this->modx->toJSON($response)
            : $response;
    }

    public function success($message = '', $data = array(), $placeholders = array())
    {
        $response = array(
            'success' => true,
            'message' => $this->modx->lexicon($message, $placeholders),
            'data'    => $data,
        );

        return $this->config['json_response']
            ? $this->modx->toJSON($response)
            : $response;
    }

    /**
     * Collects and processes any set of tags
     *
     * @param mixed   $html Source code for parse
     * @param integer $maxIterations
     *
     * @return mixed $html Parsed html
     */
    public function processTags($html, $maxIterations = 10)
    {
        $this->modx->getParser()->processElementTags('', $html, false, false, '[[', ']]', array(), $maxIterations);
        $this->modx->getParser()->processElementTags('', $html, true, true, '[[', ']]', array(), $maxIterations);

        return $html;
    }

    /**
     * from
     * https://github.com/bezumkin/Tickets/blob/9c09152ae4a1cdae04fb31d2bc0fa57be5e0c7ea/core/components/tickets/model/tickets/tickets.class.php#L1120
     *
     * Loads an instance of pdoTools
     * @return boolean
     */
    public function loadPdoTools()
    {
        if (!is_object($this->pdoTools) || !($this->pdoTools instanceof pdoTools)) {
            /** @var pdoFetch $pdoFetch */
            $fqn = $this->modx->getOption('pdoFetch.class', null, 'pdotools.pdofetch', true);
            if ($pdoClass = $this->modx->loadClass($fqn, '', false, true)) {
                $this->pdoTools = new $pdoClass($this->modx, $this->config);
            } elseif ($pdoClass = $this->modx->loadClass($fqn, MODX_CORE_PATH . 'components/pdotools/model/', false,
                true)
            ) {
                $this->pdoTools = new $pdoClass($this->modx, $this->config);
            } else {
                $this->modx->log(modX::LOG_LEVEL_ERROR,
                    'Could not load pdoFetch from "MODX_CORE_PATH/components/pdotools/model/".');
            }
        }

        return !empty($this->pdoTools) && $this->pdoTools instanceof pdoTools;
    }

    /**
     * from
     * https://github.com/bezumkin/Tickets/blob/9c09152ae4a1cdae04fb31d2bc0fa57be5e0c7ea/core/components/tickets/model/tickets/tickets.class.php#L1147
     *
     * Process and return the output from a Chunk by name.
     *
     * @param string  $name The name of the chunk.
     * @param array   $properties An associative array of properties to process the Chunk with, treated as placeholders
     *     within the scope of the Element.
     * @param boolean $fastMode If false, all MODX tags in chunk will be processed.
     *
     * @return string The processed output of the Chunk.
     */
    public function getChunk($name, array $properties = array(), $fastMode = false)
    {
        if (!$this->modx->parser) {
            $this->modx->getParser();
        }
        if (!$this->pdoTools) {
            $this->loadPdoTools();
        }

        return $this->pdoTools->getChunk($name, $properties, $fastMode);
    }

    /**
     * from
     * https://github.com/bezumkin/miniShop2/blob/master/core/components/minishop2/model/minishop2/minishop2.class.php#L472
     *
     * Function for formatting price
     *
     * @param string $price
     *
     * @return string $price
     */
    public function formatPrice($price = '0')
    {
        $pf = $this->modx->fromJSON($this->modx->getOption('ms2_price_format', null, '[2, ".", " "]'));
        if (is_array($pf)) {
            $price = number_format($price, $pf[0], $pf[1], $pf[2]);
        }
        if ($this->modx->getOption('ms2_price_format_no_zeros', null, true)) {
            if (preg_match('/\..*$/', $price, $matches)) {
                $tmp = rtrim($matches[0], '.0');
                $price = str_replace($matches[0], $tmp, $price);
            }
        }

        return $price;
    }


    /**
     * @param string $dateFormat
     *
     * @return string
     */
    public function dateFormat($date, $dateFormat = '')
    {
        $dateFormat = (empty($dateFormat))
            ? '%d %b %Y %H:%M'
            : $dateFormat;

        return strftime($dateFormat, strtotime($date));
    }

    /**
     * from
     * https://github.com/bezumkin/miniShop2/blob/186b18504214f0afde06343d9e9036c3505cf6a1/core/components/minishop2/model/minishop2/minishop2.class.php#L595
     *
     * Shorthand for original modX::invokeEvent() method with some useful additions.
     *
     * @param       $eventName
     * @param array $params
     * @param       $glue
     *
     * @return array
     */
    public function invokeEvent($eventName, array $params = array(), $glue = '<br/>')
    {
        if (isset($this->modx->event->returnedValues)) {
            $this->modx->event->returnedValues = null;
        }

        $response = $this->modx->invokeEvent($eventName, $params);
        if (is_array($response) && count($response) > 1) {
            foreach ($response as $k => $v) {
                if (empty($v)) {
                    unset($response[$k]);
                }
            }
        }

        $message = is_array($response) ? implode($glue, $response) : trim((string)$response);
        if (isset($this->modx->event->returnedValues) && is_array($this->modx->event->returnedValues)) {
            $params = array_merge($params, $this->modx->event->returnedValues);
        }

        return array(
            'success' => empty($message)
        ,
            'message' => $message
        ,
            'data'    => $params
        );
    }

    /**
     *from https://github.com/bezumkin/Tickets/blob/9c09152ae4a1cdae04fb31d2bc0fa57be5e0c7ea/core/components/tickets/model/tickets/tickets.class.php#L1040
     *
     * Adds emails to queue
     *
     * @param $uid
     * @param $subject
     * @param $body
     * @param $email
     *
     * @return bool|string
     */
    public function addQueue($uid, $subject, $body, $email = '')
    {
        $uid = (integer)$uid;
        $email = trim($email);
        if (empty($uid) && ($this->config['allowEmails'] || empty($email))) {
            return false;
        }
        /* @var msnewpricequeue $queue */
        $queue = $this->modx->newObject('msnewpricequeue', array(
                'uid'     => $uid,
                'subject' => $subject,
                'body'    => $body,
                'email'   => $email,
            )
        );

        return $queue->save();
    }

}