msoptionscolor.page.Settings = function(config) {
    config = config || {};
    Ext.applyIf(config, {
        components: [{
            xtype: 'msoptionscolor-panel-settings'
            , renderTo: 'msoptionscolor-panel-settings-div'
        }]
    });
    msoptionscolor.page.Settings.superclass.constructor.call(this, config);
};
Ext.extend(msoptionscolor.page.Settings, MODx.Component);
Ext.reg('msoptionscolor-page-settings', msoptionscolor.page.Settings);

msoptionscolor.panel.Settings = function(config) {
    config = config || {};
    Ext.apply(config, {
        border: false
        , deferredRender: true
        , baseCls: 'modx-formpanel'
        , items: [{
            html: '<h2>' + _('msoptionscolor') + ' :: ' + _('msoptionscolor_settings') + '</h2>'
            , border: false
            , cls: 'modx-page-header container'
        }, {
            xtype: 'modx-tabs'
            , id: 'msoptionscolor-settings-tabs'
            , bodyStyle: 'padding: 10px'
            , defaults: {border: false, autoHeight: true}
            , border: true
            , hideMode: 'offsets'
            , items: this.getTabs()

        }]
    });
    msoptionscolor.panel.Settings.superclass.constructor.call(this, config);
};
Ext.extend(msoptionscolor.panel.Settings, MODx.Panel, {

    getTabs: function() {
        var tabs = [];

        tabs.push({
            title: _('msoptionscolor_setting_option')
            , items: [{
                html: '<p>' + _('msoptionscolor_setting_option_intro') + '</p>'
                , border: false
                , bodyCssClass: 'panel-desc'
                , bodyStyle: 'margin-bottom: 10px'
            }, {
                xtype: 'msoptionscolor-grid-setting-option'
            }]
        });

        if (MODx.config.msoptionscolor_active_ral != 0) {
            tabs.push({
                title: _('msoptionscolor_setting_ral')
                , items: [{
                    html: '<p>' + _('msoptionscolor_setting_ral_intro') + '</p>'
                    , border: false
                    , bodyCssClass: 'panel-desc'
                    , bodyStyle: 'margin-bottom: 10px'
                }, {
                    xtype: 'msoptionscolor-grid-setting-ral'
                }]
            });
        }

        return tabs;
    }
});
Ext.reg('msoptionscolor-panel-settings', msoptionscolor.panel.Settings);