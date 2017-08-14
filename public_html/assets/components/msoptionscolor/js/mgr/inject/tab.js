if (msoptionscolor && msoptionscolor.minishop2 && msoptionscolor.minishop2.version) {
    if (msoptionscolor.minishop2.version == '2.2.0') {
        Ext.ComponentMgr.onAvailable('minishop2-product-settings-panel', function() {
            this.on('beforerender', function() {
                this.add({
                    title: _('msoptionscolor_tab_title')
                    ,hideMode: 'offsets'
                    ,items: [{xtype: 'msoptionscolor-product-color-grid'}]
                });
            });
        });
    }
    else {
        Ext.ComponentMgr.onAvailable('minishop2-product-tabs', function() {
            var tabs = this;
            tabs.on('beforerender', function() {
                var is = tabs.items.items.filter(function(row) {
                    if (row.id == 'msoptionscolor-grid-product-color') {
                        return true;
                    } else {
                        return false;
                    }
                });
                if (is.length == 0) {
                    tabs.add({
                        title: _('msoptionscolor_tab_title'),
                        bodyCssClass: 'main-wrapper',
                        items: [{
                            xtype       : 'panel',
                            layout      : 'fit',
                            items :[{
                                xtype: 'msoptionscolor-product-color-grid',
                            }]
                        }]
                    });
                }
            });
        });
    }
}





