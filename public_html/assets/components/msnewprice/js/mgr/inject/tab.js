if (msnewprice && msnewprice.minishop2 && msnewprice.minishop2.version) {
    if (msnewprice.minishop2.version == '2.2.0') {
        Ext.ComponentMgr.onAvailable('minishop2-product-settings-panel', function() {
            this.on('beforerender', function() {

                ZeroClipboard.config({moviePath: MODx.config.msnewprice_assets_path+'js/mgr/misc/zeroclipboard/ZeroClipboard.swf'});
                var client = new ZeroClipboard();

                var listeners = {
                    change:{fn:MODx.fireResourceFormChange}
                    ,select:{fn:MODx.fireResourceFormChange}
                    ,keydown:{fn:MODx.fireResourceFormChange}
                    ,check:{fn:MODx.fireResourceFormChange}
                    ,uncheck:{fn:MODx.fireResourceFormChange}
                };

                var offset_startdate = MODx.config.msnewprice_offset_startdate;
                var offset_stopdate = MODx.config.msnewprice_offset_stopdate;

                var startdate = new Date();
                startdate.setTime(startdate.getTime() + offset_startdate*3600*1000);
                startdate.setHours(0,0,0,0);
                var stopdate = new Date();
                stopdate.setTime(stopdate.getTime() + offset_stopdate*3600*1000);
                stopdate.setHours(0,0,0,0);

                this.add({
                    title: _('msnewprice_title')
                    ,hideMode: 'offsets'
                    ,items: [
                        {
                            html: _('msnewprice_intro_msg'),
                            cls: 'modx-page-header container',
                            border: false
                        }, {
                            layout: 'column',
                            border: false,
                            bodyCssClass: 'tab-panel-wrapper ',
                            items: [{
                                columnWidth: .4
                                ,xtype: 'panel'
                                ,border: false
                                ,layout: 'form'
                                ,labelAlign: 'top'
                                ,preventRender: true
                                ,items: [
                                    {xtype: 'numberfield', id: 'msn-price-field', decimalPrecision: 2, allowNegative:false, value: msnewprice.data.msn_newprice || 0, name: 'msn[newprice]', description: _('msnewprice_newprice_desc'), fieldLabel: _('msnewprice_newprice'), anchor: '80%', enableKeyEvents: true
                                        ,listeners: {
                                        change:{fn:MODx.fireResourceFormChange}
                                        ,select:{fn:MODx.fireResourceFormChange}
                                        ,keydown:{fn:MODx.fireResourceFormChange}
                                        ,check:{fn:MODx.fireResourceFormChange}
                                        ,uncheck:{fn:MODx.fireResourceFormChange}
                                        ,render: function(field){
                                            client.on( 'copy', function(e) {
                                                var price = field.getValue();
                                                client.setText(price.toString());
                                            });
                                        }
                                    }
                                    }
                                    ,{xtype: 'button', id: 'copyBtn', text: _('msnewprice_copy'), padding: 0, style: 'margin-top: 10px; padding: 5px'
                                        ,listeners: {
                                            render: function(button){
                                                client.clip(button.getEl().dom);
                                            }
                                        }
                                    }
                                    ,{xtype: 'button', id: 'copyPriceBtn', text: _('msnewprice_copy_price'), padding: 0, style: 'margin: 10px 0 0 10px; padding: 5px'
                                        ,handler: function(){
                                            var price = Ext.getCmp('modx-resource-price').getValue();
                                            Ext.getCmp('msn-price-field').setValue(price);
                                        }
                                    }
                                    ,{xtype: 'datefield', name: 'msn[startdate]', description: _('msnewprice_startdate_desc'), fieldLabel: _('msnewprice_startdate'), anchor: '80%', format: 'Y-m-d H:i:s',listeners: listeners, value: msnewprice.data.msn_startdate || startdate }
                                    ,{xtype: 'datefield', name: 'msn[stopdate]', description: _('msnewprice_stopdate_desc'), fieldLabel: _('msnewprice_stopdate'), anchor: '80%',  format: 'Y-m-d H:i:s',listeners: listeners, value: msnewprice.data.msn_stopdate || stopdate}
                                    ,{xtype: 'textarea', name: 'msn[description]', description: _('msnewprice_description_desc'), fieldLabel: _('msnewprice_description'), anchor: '80%', enableKeyEvents: true, listeners: listeners, value: msnewprice.data.msn_description || ''}
                                ]
                            }, {
                                columnWidth: .4
                                ,xtype: 'panel'
                                ,border: false
                                ,layout: 'form'
                                ,labelAlign: 'top'
                                ,preventRender: true
                                ,items:[{
                                    xtype: 'checkboxgroup'
                                    ,fieldLabel: _('msnewprice_options')
                                    ,items: [
                                        {xtype: 'xcheckbox', boxLabel: _('msnewprice_action'), name: 'msn[action]', description: _('msnewprice_action_desc'), id: 'msnewprice-action-price',listeners: listeners,  inputValue: 1, checked: (msnewprice.data.msn_action == true) ? 1 : (msnewprice.data.msn_action == false) ? 0 : parseInt(MODx.config.msnewprice_action)}
                                        ,{xtype: 'xcheckbox', boxLabel: _('msnewprice_overwrite'), name: 'msn[overwrite]', description: _('msnewprice_overwrite_desc'), id: 'msnewprice-overwrite-price',listeners: listeners,  inputValue: 1, checked: (msnewprice.data.msn_overwrite == true) ? 1 : (msnewprice.data.msn_overwrite == false) ? 0 : parseInt(MODx.config.msnewprice_overwrite)}
                                    ]}
                                ]}
                            ]}

                    ]
                });
            });
        });
    }
    else {
        Ext.ComponentMgr.onAvailable('minishop2-product-tabs', function() {
            var tabs = this;
            tabs.on('beforerender', function() {
                var is = tabs.items.items.filter(function(row) {
                    if (row.id == 'msnewprice-product-price') {
                        return true;
                    } else {
                        return false;
                    }
                });
                if (is.length == 0) {

                    ZeroClipboard.config({moviePath: MODx.config.msnewprice_assets_path+'js/mgr/misc/zeroclipboard/ZeroClipboard.swf'});
                    var client = new ZeroClipboard();

                    var listeners = {
                        change:{fn:MODx.fireResourceFormChange}
                        ,select:{fn:MODx.fireResourceFormChange}
                        ,keydown:{fn:MODx.fireResourceFormChange}
                        ,check:{fn:MODx.fireResourceFormChange}
                        ,uncheck:{fn:MODx.fireResourceFormChange}
                    };

                    var offset_startdate = MODx.config.msnewprice_offset_startdate;
                    var offset_stopdate = MODx.config.msnewprice_offset_stopdate;

                    var startdate = new Date();
                    startdate.setTime(startdate.getTime() + offset_startdate*3600*1000);
                    startdate.setHours(0,0,0,0);
                    var stopdate = new Date();
                    stopdate.setTime(stopdate.getTime() + offset_stopdate*3600*1000);
                    stopdate.setHours(0,0,0,0);

                    tabs.add({
                        title: _('msnewprice_title'),
                        bodyCssClass: 'main-wrapper',
                        id: 'msnewprice-product-price',
                        items: [
                            {
                                html: _('msnewprice_intro_msg'),
                                cls: 'modx-page-header container',
                                border: false
                            }, {
                                layout: 'column',
                                border: false,
                                bodyCssClass: 'tab-panel-wrapper ',
                                items: [{
                                    columnWidth: .4
                                    ,xtype: 'panel'
                                    ,border: false
                                    ,layout: 'form'
                                    ,labelAlign: 'top'
                                    ,preventRender: true
                                    ,items: [
                                        {xtype: 'numberfield', id: 'msn-price-field', decimalPrecision: 2, allowNegative:false, value: msnewprice.data.msn_newprice || 0, name: 'msn[newprice]', description: _('msnewprice_newprice_desc'), fieldLabel: _('msnewprice_newprice'), anchor: '80%', enableKeyEvents: true
                                            ,listeners: {
                                            change:{fn:MODx.fireResourceFormChange}
                                            ,select:{fn:MODx.fireResourceFormChange}
                                            ,keydown:{fn:MODx.fireResourceFormChange}
                                            ,check:{fn:MODx.fireResourceFormChange}
                                            ,uncheck:{fn:MODx.fireResourceFormChange}
                                            ,render: function(field){
                                                client.on( 'copy', function(e) {
                                                    var price = field.getValue();
                                                    client.setText(price.toString());
                                                });
                                            }
                                        }
                                        }
                                      /*  ,{xtype: 'button', id: 'copyBtn', text: _('msnewprice_copy'), padding: 0, style: 'margin-top: 10px; padding: 5px'
                                            ,listeners: {
                                                render: function(button){
                                                    client.clip(button.getEl().dom);
                                                }
                                            }
                                        }*/
                                        ,{xtype: 'button', id: 'copyPriceBtn', text: _('msnewprice_copy_price'), padding: 0, style: 'margin: 10px 0 0 0px; padding: 5px'
                                            ,handler: function(){
                                                var price = Ext.getCmp('modx-resource-price').getValue();
                                                Ext.getCmp('msn-price-field').setValue(price);
                                            }
                                        }
                                        ,{xtype: 'datefield', name: 'msn[startdate]', description: _('msnewprice_startdate_desc'), fieldLabel: _('msnewprice_startdate'), anchor: '80%', format: 'Y-m-d H:i:s',listeners: listeners, value: msnewprice.data.msn_startdate || startdate }
                                        ,{xtype: 'datefield', name: 'msn[stopdate]', description: _('msnewprice_stopdate_desc'), fieldLabel: _('msnewprice_stopdate'), anchor: '80%',  format: 'Y-m-d H:i:s',listeners: listeners, value: msnewprice.data.msn_stopdate || stopdate}
                                        ,{xtype: 'textarea', name: 'msn[description]', description: _('msnewprice_description_desc'), fieldLabel: _('msnewprice_description'), anchor: '80%', enableKeyEvents: true, listeners: listeners, value: msnewprice.data.msn_description || ''}
                                    ]
                                }, {
                                    columnWidth: .4
                                    ,xtype: 'panel'
                                    ,border: false
                                    ,layout: 'form'
                                    ,labelAlign: 'top'
                                    ,preventRender: true
                                    ,items:[{
                                        xtype: 'checkboxgroup'
                                        ,fieldLabel: _('msnewprice_options')
                                        ,items: [
                                            {xtype: 'xcheckbox', boxLabel: _('msnewprice_action'), name: 'msn[action]', description: _('msnewprice_action_desc'), id: 'msnewprice-action-price',listeners: listeners,  inputValue: 1, checked: (msnewprice.data.msn_action == true) ? 1 : (msnewprice.data.msn_action == false) ? 0 : parseInt(MODx.config.msnewprice_action)}
                                            ,{xtype: 'xcheckbox', boxLabel: _('msnewprice_overwrite'), name: 'msn[overwrite]', description: _('msnewprice_overwrite_desc'), id: 'msnewprice-overwrite-price',listeners: listeners,  inputValue: 1, checked: (msnewprice.data.msn_overwrite == true) ? 1 : (msnewprice.data.msn_overwrite == false) ? 0 : parseInt(MODx.config.msnewprice_overwrite)}
                                        ]}
                                    ]}
                                ]}

                        ]
                    });
                }
            });
        });
    }
}



