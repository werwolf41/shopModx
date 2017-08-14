<?php

/**
 * Class msoptionscolorMainController
 */
abstract class msoptionscolorMainController extends modExtraManagerController
{
    /** @var msoptionscolor $msoptionscolor */
    public $msoptionscolor;


    /**
     * @return void
     */
    public function initialize()
    {
        $corePath = $this->modx->getOption('msoptionscolor_core_path', null,
            $this->modx->getOption('core_path') . 'components/msoptionscolor/');
        require_once $corePath . 'model/msoptionscolor/msoptionscolor.class.php';

        $this->msoptionscolor = new msoptionscolor($this->modx);
        $this->addCss($this->msoptionscolor->config['cssUrl'] . 'mgr/main.css');
        $this->addJavascript($this->msoptionscolor->config['jsUrl'] . 'mgr/msoptionscolor.js');
        $this->addHtml('
		<script type="text/javascript">
			msoptionscolor.config = ' . $this->modx->toJSON($this->msoptionscolor->config) . ';
			msoptionscolor.config.connector_url = "' . $this->msoptionscolor->config['connectorUrl'] . '";
		</script>
		');

        parent::initialize();
    }


    /**
     * @return array
     */
    public function getLanguageTopics()
    {
        return array('msoptionscolor:default');
    }


    /**
     * @return bool
     */
    public function checkPermissions()
    {
        return true;
    }
}


/**
 * Class IndexManagerController
 */
class IndexManagerController extends msoptionscolorMainController
{

    /**
     * @return string
     */
    public static function getDefaultController()
    {
        return 'home';
    }
}