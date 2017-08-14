<?php

require_once dirname(dirname(dirname(__FILE__))) . '/index.class.php';

class ControllersSettingsManagerController extends msoptionscolorMainController
{

    public static function getDefaultController()
    {
        return 'settings';
    }

}

class msoptionscolorSettingsManagerController extends msoptionscolorMainController
{

    public function getPageTitle()
    {
        return $this->modx->lexicon('msoptionscolor') . ' :: ' . $this->modx->lexicon('msoptionscolor_settings');
    }

    public function getLanguageTopics()
    {
        return array('msoptionscolor:default', 'msoptionscolor:manager');
    }

    public function loadCustomCssJs()
    {
        $this->addJavascript(MODX_MANAGER_URL . 'assets/modext/util/datetime.js');
        $this->addJavascript($this->msoptionscolor->config['jsUrl'] . 'mgr/misc/msoptionscolor.utils.js');
        $this->addJavascript($this->msoptionscolor->config['jsUrl'] . 'mgr/misc/msoptionscolor.combo.js');
        $this->addJavascript($this->msoptionscolor->config['jsUrl'] . 'mgr/settings/option.grid.js');

        $this->addCss($this->msoptionscolor->config['cssUrl'] . 'mgr/main.css');
        $this->addCss($this->msoptionscolor->config['cssUrl'] . 'mgr/colorpicker/colorpicker.css');
        $this->addJavascript($this->msoptionscolor->config['jsUrl'] . 'mgr/colorpicker/colorpicker.js');
        $this->addJavascript($this->msoptionscolor->config['jsUrl'] . 'mgr/colorpicker/colorpicker.field.js');

        $this->addJavascript($this->msoptionscolor->config['jsUrl'] . 'mgr/settings/ral.grid.js');
        $this->addJavascript($this->msoptionscolor->config['jsUrl'] . 'mgr/settings/settings.panel.js');
        //
        $this->addHtml(str_replace('			', '', '
			<script type="text/javascript">


				Ext.onReady(function() {
					MODx.load({ xtype: "msoptionscolor-page-settings"});
				});

			</script>'
        ));
    }

    public function getTemplateFile()
    {
        return $this->msoptionscolor->config['templatesPath'] . 'mgr/settings.tpl';
    }

}

// MODX 2.3
class ControllersMgrSettingsManagerController extends ControllersSettingsManagerController
{

    public static function getDefaultController()
    {
        return 'mgr/settings';
    }

}

class msoptionscolorMgrSettingsManagerController extends msoptionscolorSettingsManagerController
{

}
