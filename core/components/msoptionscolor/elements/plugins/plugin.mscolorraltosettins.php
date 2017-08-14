<?php
switch ($modx->event->name) {
    case 'OnManagerPageBeforeRender':
        $msoptionscolor = $modx->getService('msoptionscolor', 'msoptionscolor',
            $modx->getOption('msoptionscolor_core_path', null,
                $modx->getOption('core_path') . 'components/msoptionscolor/') . 'model/msoptionscolor/');
        if (!($msoptionscolor instanceof msoptionscolor)) {
            return '';
        }
        if (!$config = $controller->config) {
            return '';
        }
        if (($config['namespace'] != 'minishop2')) {
            return '';
        }
        if ((!in_array($config['controller'], array('controllers/mgr/settings')))) {
            return '';
        }
    
        $modx->controller->addLexiconTopic('msoptionscolor:default,msoptionscolor:manager');
        $modx->controller->addCss($msoptionscolor->config['cssUrl'] . 'mgr/main.css');
        $modx->controller->addCss($msoptionscolor->config['cssUrl'] . 'mgr/colorpicker/colorpicker.css');
        $modx->controller->addJavascript($msoptionscolor->config['jsUrl'] . 'mgr/msoptionscolor.js');
        $modx->controller->addJavascript($msoptionscolor->config['jsUrl'] . 'mgr/misc/msoptionscolor.utils.js');
        $modx->controller->addJavascript($msoptionscolor->config['jsUrl'] . 'mgr/misc/msoptionscolor.utils.js');
        $modx->controller->addJavascript($msoptionscolor->config['jsUrl'] . 'mgr/misc/msoptionscolor.combo.js');
        $modx->controller->addJavascript($msoptionscolor->config['jsUrl'] . 'mgr/colorpicker/colorpicker.js');
        $modx->controller->addJavascript($msoptionscolor->config['jsUrl'] . 'mgr/colorpicker/colorpicker.field.js');
        $modx->controller->addJavascript($msoptionscolor->config['jsUrl'] . 'mgr/settings/ral.grid.js');
        $modx->controller->addHtml('<script type="text/javascript">
			msoptionscolor.config = ' . $modx->toJSON($msoptionscolor->config) . ';
			msoptionscolor.config.connector_url = "' . $msoptionscolor->config['connectorUrl'] . '";

			var deliveryId = "minishop2-grid-delivery";
			Ext.ComponentMgr.onAvailable(deliveryId, function() {
				var ms2settings = Ext.getCmp(deliveryId).findParentByType("panel").findParentByType("panel");
				ms2settings.on("beforerender", function() {
					this.add({
						title: _("msoptionscolor_setting_ral"),
						items: [{
							html: "<p>" + _("msoptionscolor_setting_ral_intro") + "</p>",
							border: false,
							bodyCssClass: "panel-desc",
							bodyStyle: "margin-bottom: 10px"
						}, {
							xtype: "msoptionscolor-grid-setting-ral"
						}]
					});
				});
			});
		</script>');
        break;
}