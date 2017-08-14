<?php

/**
 * The home manager controller for msnewprice.
 *
 */
class msnewpriceHomeManagerController extends msnewpriceMainController
{
    /* @var msnewprice $msnewprice */
    public $msnewprice;


    /**
     * @param array $scriptProperties
     */
    public function process(array $scriptProperties = array())
    {
    }


    /**
     * @return null|string
     */
    public function getPageTitle()
    {
        return $this->modx->lexicon('msnewprice');
    }


    /**
     * @return void
     */
    public function loadCustomCssJs()
    {
        $this->addCss($this->msnewprice->config['cssUrl'] . 'mgr/main.css');
        $this->addCss($this->msnewprice->config['cssUrl'] . 'mgr/bootstrap.buttons.css');
        $this->addJavascript($this->msnewprice->config['jsUrl'] . 'mgr/misc/utils.js');
        $this->addJavascript($this->msnewprice->config['jsUrl'] . 'mgr/widgets/items.grid.js');
        $this->addJavascript($this->msnewprice->config['jsUrl'] . 'mgr/widgets/items.windows.js');
        $this->addJavascript($this->msnewprice->config['jsUrl'] . 'mgr/widgets/home.panel.js');
        $this->addJavascript($this->msnewprice->config['jsUrl'] . 'mgr/sections/home.js');
        $this->addHtml('<script type="text/javascript">
		Ext.onReady(function() {
			MODx.load({ xtype: "msnewprice-page-home"});
		});
		</script>');
    }


    /**
     * @return string
     */
    public function getTemplateFile()
    {
        return $this->msnewprice->config['templatesPath'] . 'home.tpl';
    }
}