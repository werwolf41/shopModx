<?php

/**
 * Class msnewpriceMainController
 */
abstract class msnewpriceMainController extends modExtraManagerController
{
    /** @var msnewprice $msnewprice */
    public $msnewprice;


    /**
     * @return void
     */
    public function initialize()
    {
        $corePath = $this->modx->getOption('msnewprice_core_path', null,
            $this->modx->getOption('core_path') . 'components/msnewprice/');
        require_once $corePath . 'model/msnewprice/msnewprice.class.php';

        $this->msnewprice = new msnewprice($this->modx);
        $this->addCss($this->msnewprice->config['cssUrl'] . 'mgr/main.css');
        $this->addJavascript($this->msnewprice->config['jsUrl'] . 'mgr/msnewprice.js');
        $this->addHtml('
		<script type="text/javascript">
			msnewprice.config = ' . $this->modx->toJSON($this->msnewprice->config) . ';
			msnewprice.config.connector_url = "' . $this->msnewprice->config['connectorUrl'] . '";
		</script>
		');

        parent::initialize();
    }


    /**
     * @return array
     */
    public function getLanguageTopics()
    {
        return array('msnewprice:default');
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
class IndexManagerController extends msnewpriceMainController
{

    /**
     * @return string
     */
    public static function getDefaultController()
    {
        return 'home';
    }
}