<?php

/**
 * The base class for msoptionscolor.
 */
class msoptionscolor
{
    /* @var modX $modx */
    public $modx;
    public $namespace = 'msoptionscolor';
    public $cache = null;
    public $config = array();
    public $initialized = array();
    public $active = false;
    public $ms2;

    /**
     * @param modX  $modx
     * @param array $config
     */
    function __construct(modX &$modx, array $config = array())
    {
        $this->modx =& $modx;

        $this->namespace = $this->getOption('namespace', $config, 'msoptionscolor');
        $corePath = $this->modx->getOption('msoptionscolor_core_path', $config,
            $this->modx->getOption('core_path') . 'components/msoptionscolor/');
        $assetsUrl = $this->modx->getOption('msoptionscolor_assets_url', $config,
            $this->modx->getOption('assets_url') . 'components/msoptionscolor/');
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

            'json_response' => true,
            //'webconnector' => $assetsUrl . 'web-connector.php',

            'frontendCss' => $this->modx->getOption('msoptionscolor_front_css', null,
                '[[+assetsUrl]]css/web/default.css'),
            'frontendJs'  => $this->modx->getOption('msoptionscolor_front_js', null, '[[+assetsUrl]]js/web/default.js'),

        ), $config);

        $this->modx->addPackage('msoptionscolor', $this->config['modelPath']);
        $this->modx->lexicon->load('msoptionscolor:default');
        $this->active = $this->modx->getOption('msoptionscolor_active', $config, false);

        if (!$this->ms2 = $modx->getService('miniShop2')) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'msOptionsColor requires installed miniShop2.');

            return false;
        }
    }

    /**
     * @param       $key
     * @param array $config
     * @param null  $default
     *
     * @return mixed|null
     */
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

    /**
     * @param string $ctx
     * @param array  $scriptProperties
     *
     * @return bool
     */
    public function initialize($ctx = 'web', $scriptProperties = array())
    {
        $this->config = array_merge($this->config, $scriptProperties);
        $this->config['ctx'] = $ctx;
        if (!empty($this->initialized[$ctx])) {
            return true;
        }
        switch ($ctx) {
            case 'mgr':
                break;
            default:
                if (!defined('MODX_API_MODE') || !MODX_API_MODE) {
                    if ($css = trim($this->config['frontendCss'])) {
                        if (preg_match('/\.css/i', $css)) {
                            $this->modx->regClientCSS(str_replace('[[+assetsUrl]]', $this->config['assetsUrl'], $css));
                        }
                    }
                    $config_js = preg_replace(array('/^\n/', '/\t{5}/'), '', '
						msoptionscolor = {};
						msoptionscolorConfig = {
							jsUrl: "' . $this->config['jsUrl'] . 'web/"
							,ctx: "' . $this->modx->context->get('key') . '"
						};
					');
                    $this->modx->regClientStartupScript("<script type=\"text/javascript\">\n" . $config_js . "\n</script>",
                        true);
                    if ($js = trim($this->config['frontendJs'])) {
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

    /**
     * @param $sp
     */
    public function onDocFormPrerender($sp)
    {
        // lexicon
        $this->modx->controller->addLexiconTopic('msoptionscolor:default,msoptionscolor:manager');
        // css
        $this->modx->regClientCSS($this->getOption('cssUrl') . 'mgr/main.css');
        $this->modx->regClientCSS($this->getOption('cssUrl') . 'mgr/colorpicker/colorpicker.css');
        // js
        $this->modx->regClientStartupScript($this->getOption('jsUrl') . 'mgr/msoptionscolor.js');
        $this->modx->regClientStartupScript($this->getOption('jsUrl') . 'mgr/misc/msoptionscolor.utils.js');
        $this->modx->regClientStartupScript($this->getOption('jsUrl') . 'mgr/misc/msoptionscolor.combo.js');
        $this->modx->regClientStartupScript($this->getOption('jsUrl') . 'mgr/colorpicker/colorpicker.js');
        $this->modx->regClientStartupScript($this->getOption('jsUrl') . 'mgr/colorpicker/colorpicker.field.js');
        //
        $minishop2Version = isset($this->ms2->version) ? $this->ms2->version : '2.2.0';

        $data_js = preg_replace(array('/^\n/', '/\t{6}/'), '', '
			msoptionscolor.config.connector_url = "' . $this->config['connectorUrl'] . '";
			msoptionscolor.product_id = ' . (int)$sp['id'] . ';
			msoptionscolor.minishop2 = {};
			msoptionscolor.minishop2.version = "' . $minishop2Version . '";
		');
        $this->modx->regClientStartupScript("<script type=\"text/javascript\">\n" . $data_js . "\n</script>", true);
        // inject
        if (!$this->modx->getObject('msProduct', $sp['id']) OR $this->modx->getOption('mode', $sp) !== 'upd') {
            return;
        }

        $this->modx->regClientStartupScript($this->getOption('jsUrl') . 'mgr/inject/color.grid.js');
        $this->modx->regClientStartupScript($this->getOption('jsUrl') . 'mgr/inject/tab.js');
    }

    /**
     * @param string $message
     * @param array  $data
     * @param array  $placeholders
     *
     * @return array|string
     */
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
            'message' => $this->modx->lexicon($message, $placeholders),
            'data'    => $data,
        );

        return $this->config['json_response']
            ? $this->modx->toJSON($response)
            : $response;
    }

}