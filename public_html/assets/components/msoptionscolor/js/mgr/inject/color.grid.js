msoptionscolor.grid.Color = function(config) {
    config = config || {};
    config.product_id = msoptionscolor.product_id;

    this.sm = new Ext.grid.CheckboxSelectionModel();
    this.dd = function(grid) {
        this.dropTarget = new Ext.dd.DropTarget(grid.container, {
            ddGroup : 'dd',
            copy:false,
            notifyDrop : function(dd, e, data) {
                var store = grid.store.data.items;
                var target = store[dd.getDragData(e).rowIndex].id;
                var source = store[data.rowIndex].id;
                if (target != source) {
                    dd.el.mask(_('loading'),'x-mask-loading');
                    MODx.Ajax.request({
                        url: msoptionscolor.config.connector_url
                        ,params: {
                            action: config.action || 'mgr/color/sort'
                            ,source: source
                            ,target: target
                            ,product_id: msoptionscolor.product_id
                        }
                        ,listeners: {
                            success: {fn:function(r) {dd.el.unmask();grid.refresh();},scope:grid}
                            ,failure: {fn:function(r) {dd.el.unmask();},scope:grid}
                        }
                    });
                }
            }
        });
    };
    if (!config.menu) {
        config.menu = [];
    }
    config.menu.push(
        {text: _('msoptionscolor_color_selected_set_active'),handler: this.activeSelected,scope: this}
        ,{text: _('msoptionscolor_color_selected_set_inactive'),handler: this.inactiveSelected,scope: this}
    );
    config.menu.push('-');
    config.menu.push(
        {text: _('msoptionscolor_color_selected_delete'),handler: this.deleteSelected,scope: this}
    );


    if (!config.tbar) {
        config.tbar = [];
    }
    config.tbar.push({
        text: '<i class="icon icon-list"></i> ' + _('msoptionscolor_bulk_actions')
        ,menu: config.menu
    },'-',{
        text: '<i class="icon icon-plus"></i> ' + _('msoptionscolor_btn_create')
        ,handler: this.createColor
        ,scope: this
    },'->',{
        xtype: 'msoptionscolor-combo-option'
        ,id: 'msbatcheditor-combo-active-selection'
        ,name: 'active'
        ,queryParam: 'active'
        ,addall:1
        ,width: 160
        ,listeners: {
            select: {fn: this.setOptionFilter, scope:this}
        }
    });


    Ext.applyIf(config,{
        id: 'msoptionscolor-grid-product-color'
        ,url: msoptionscolor.config.connector_url
        ,baseParams: {
            action: 'mgr/color/getlist'
            ,product_id: config.product_id
            ,sort: 'rank'
        }
        ,fields: ['id','product_id','option','value','color','pattern','rank','active','option_name','ral']
        ,autoHeight: true
        ,paging: true
        ,remoteSort: true
        ,columns: this.getColumns()
        ,sm: this.sm

        ,save_action: 'mgr/color/updatefromgrid'
        ,autosave: true
        ,save_callback: this.updateRow
        ,ddGroup: 'dd'
        ,enableDragDrop: true
		,listeners: {
			rowDblClick: function(grid, rowIndex, e) {
				var row = grid.store.getAt(rowIndex);
				this.updateColor(grid, e, row);
			}
            ,render: {fn: this.dd, scope: this}
		}

    });
    msoptionscolor.grid.Color.superclass.constructor.call(this,config);
};
Ext.extend(msoptionscolor.grid.Color,MODx.grid.Grid, {

    getMenu: function() {
        var m = [];
        m.push({
            text: _('msoptionscolor_menu_remove')
            ,handler: this.removeColor
        });
        m.push({
            text: _('msoptionscolor_menu_update')
            ,handler: this.updateColor
        });
        this.addContextMenuItem(m);
    }

    ,getColumns: function() {
        var columns = [this.sm];

        columns.push(
            {header: _('msoptionscolor_id'), dataIndex: 'id',width: 25, sortable: true}
            //,{header: _('msoptionscolor_id'), dataIndex: 'product_id',width: 25, sortable: true}
            ,{header: _('msoptionscolor_option'), dataIndex: 'option_name',width: 75, sortable: true}
            ,{header: _('msoptionscolor_value'), dataIndex: 'value',width: 75, sortable: true}
            ,{header: _('msoptionscolor_color'), dataIndex: 'color',width: 75, sortable: true,renderer: msoptionscolor.utils.renderColor/*,editor:{xtype:'colorpickerfield'}*/}
			,{header: _('msoptionscolor_pattern'), dataIndex: 'pattern',width: 75, sortable: true,renderer: msoptionscolor.utils.renderPattern/*,editor:{xtype:'msoptionscolor-combo-browser'}*/}
        );
        if (MODx.config.msoptionscolor_active_ral != 0) {
            columns.push(
                {header: _('msoptionscolor_ral'),dataIndex: 'ral',sortable:true, width:50/*, renderer: msoptionscolor.utils.renderBoolean*//*, editor:{xtype:'combo-boolean'}*/}
            );
        }
        columns.push(
            {header: _('msoptionscolor_active'),dataIndex: 'active',sortable:true, width:50, renderer: msoptionscolor.utils.renderBoolean/*, editor:{xtype:'combo-boolean'}*/}
        );

        return columns;
    }

    ,setOptionFilter: function(cb) {
        this.getStore().baseParams['option'] = cb.value;
        this.getBottomToolbar().changePage(1);
        //this.refresh();
    }

    ,updateRow: function(response) {
        var row = response.object;
        var items = this.store.data.items;

        for (var i = 0; i < items.length; i++) {
            var item = items[i];
            if (item.id == row.id) {
                item.data = row;
            }
        }
    }

    ,createColor: function(btn,e) {

        var product_id = btn.scope.config.product_id;

        if (this.windows.createColor) {
            try {
                this.windows.createColor.close();
                this.windows.createColor.destroy();
                this.windows.createColor = undefined;
            } catch (e) {}
        }

        if (!this.windows.createColor) {
            this.windows.createColor = MODx.load({
                xtype: 'msoptionscolor-window-color-create'
                ,title: _('msoptionscolor_btn_create')
                ,fields: this.getColorFields('create')
                ,baseParams: {
                    action: 'mgr/color/create'
                }
                ,listeners: {
                    success: {fn:function() { this.refresh(); },scope:this}
                }
            });
        }
        this.windows.createColor.fp.getForm().reset();
        this.windows.createColor.fp.getForm().setValues({
            product_id: product_id
            ,active: 1
        });
        this.windows.createColor.show(e.target);
    }

    ,updateColor: function(btn,e,row) {
        if (typeof(row) != 'undefined') {this.menu.record = row.data;}
        var id = this.menu.record.id;
        /*var product_id = btn.scope.config.product_id;*/

        MODx.Ajax.request({
            url: msoptionscolor.config.connector_url
            ,params: {
                action: 'mgr/color/get'
                ,id: id
            }
            ,listeners: {
                success: {fn:function(r) {

                    if (this.windows.updateColor) {
                        try {
                            this.windows.updateColor.close();
                            this.windows.updateColor.destroy();
                        } catch (e) {}
                    }
                    this.windows.updateColor = MODx.load({
                        xtype: 'msoptionscolor-window-color-update'
                        ,record:r.object
                        ,fields: this.getColorFields('update')
                        ,listeners: {
                            success: {fn:function() {this.refresh();},scope:this}
                        }
                    });
                    r.object.color = '';
                    this.windows.updateColor.fp.getForm().reset();
                    this.windows.updateColor.fp.getForm().setValues(r.object);
                    this.windows.updateColor.show(e.target);
                },scope:this}
            }
        });
    }

    ,handleChangeOption: function(type) {
        var el = Ext.getCmp('msoptionscolor-product-option-'+type);
        var option = Ext.getCmp('msoptionscolor-minishop2-combo-one-options-'+type);
        option.store.baseParams.option_id = el.value;
        option.removeAllItems();
        option.enable();
        option.enable();

    }

	,handlePattern: function(type) {
		var i = Ext.get('msoptionscolor-product-pattern-text-'+type);
		var ii = Ext.getCmp('msoptionscolor-product-pattern-text-'+type);
		if (i) {
			i.applyStyles('background-image: url(/' + ii.originalValue + ');background-repeat:repeat-x;');
		}

	}

	,handleHideOption: function(type) {
		var el = Ext.getCmp('msoptionscolor-product-option-'+type);
		var option = Ext.getCmp('msoptionscolor-minishop2-combo-one-options-'+type);
		if(option) {option.disable();}
	}

    ,getColorFields: function(type) {

        var fields = [];
        var disabled = type == 'update' ? true : false;

        var value = type == 'update'
            ? {
            xtype: 'textfield',
            name: 'value',
            readOnly: true,
            fieldLabel: _('msoptionscolor_value'),
            anchor: '99%',
            msgTarget: 'under',
            product_id: msoptionscolor.product_id
        }
            : {
            xtype: 'msoptionscolor-minishop2-combo-one-options',
            id: 'msoptionscolor-minishop2-combo-one-options-' + type,
            name: 'value',
            fieldLabel: _('msoptionscolor_value'),
            description: _('msoptionscolor_value_help'),
            emptyText: _('msoptionscolor_select_empty'),
            anchor: '99%',
            msgTarget: 'under',
            product_id: msoptionscolor.product_id,
            option_id: '1'
        };

        fields.push(
            {xtype: 'hidden',name: 'id', id: 'msoptionscolor-product-color-id-'+type}
            ,{xtype: 'hidden',name: 'product_id', id: 'msoptionscolor-product-product_id-'+type}
            ,{xtype: 'msoptionscolor-combo-option', disabled: disabled, fieldLabel: _('msoptionscolor_key'), name: 'option', hiddenName: 'option', allowBlank: false, anchor: '99%', id: 'msoptionscolor-product-option-'+type,
                listeners: {
                    afterrender: {fn: function(r) { this.handleHideOption(type);},scope:this }
                    ,select: {fn: function(r) { this.handleChangeOption(type);},scope:this }
                }
            }
            ,value
        );
        if (MODx.config.msoptionscolor_active_ral != 0) {
            fields.push(
                {xtype: 'msoptionscolor-combo-ral', fieldLabel: _('msoptionscolor_ral'), name: 'ral', hiddenName: 'ral', allowBlank: true, anchor: '99%', id: 'msoptionscolor-product-ral-text-'+type}
            );
        }
        fields.push(
            {xtype: 'colorpickerfield', fieldLabel: _('msoptionscolor_color'), name: 'color', hiddenName: 'color', allowBlank: true, anchor: '99%', id: 'msoptionscolor-product-color-text-'+type}
            ,{xtype: 'msoptionscolor-combo-browser', fieldLabel: _('msoptionscolor_pattern'), name: 'pattern', hiddenName: 'pattern', allowBlank: true, anchor: '99%', id: 'msoptionscolor-product-pattern-text-'+type,
				listeners: {
					afterrender: {fn: function(r) { this.handlePattern(type);},scope:this }
				}
			}
       );
        fields.push(
            {xtype: 'xcheckbox', boxLabel: _('msoptionscolor_active'), name: 'active', id: 'msoptionscolor-product-option-active-'+type}
        );
        if(disabled) {
            fields.push(
                {xtype: 'hidden',name: 'option_',hiddenName: 'option_', id: 'msoptionscolor-product-option_-'+type}
            );
        }

        return fields;
    }

    ,removeColor: function(btn,e) {
        if (this.menu.record) {
            MODx.msg.confirm({
                title: _('msoptionscolor_menu_remove')
                ,text: _('msoptionscolor_menu_remove_confirm')
                ,url: msoptionscolor.config.connector_url
                ,params: {
                    action: 'mgr/color/remove'
                    ,id: this.menu.record.id
                }
                ,listeners: {
                    success: {fn:function(r) { this.refresh(); },scope:this}
                }
            });
        }
    }

    ,deleteSelected: function(btn,e) {
        var cs = this.getSelectedAsList(); 
        if (cs === false) return false;

        MODx.msg.confirm({
            title: _('msoptionscolor_menu_remove')
            ,text: _('msoptionscolor_menu_remove_multiple_confirm')
            ,url: msoptionscolor.config.connector_url
            ,params: {
                action: 'mgr/color/delete_multiple'
                ,ids: cs
            }
            ,listeners: {
                'success': {fn:function(r) {
                    this.getSelectionModel().clearSelections(true);
                    this.refresh();
                },scope:this}
            }
        });

        return true;
    }

    ,activeSelected: function(btn,e) {
        var cs = this.getSelectedAsList();
        if (cs === false) return false;

        MODx.msg.confirm({
            title: _('msoptionscolor_menu_active')
            ,text: _('msoptionscolor_menu_active_multiple_confirm')
            ,url: msoptionscolor.config.connector_url
            ,params: {
                action: 'mgr/color/active_multiple'
                ,ids: cs
                ,value: 1
            }
            ,listeners: {
                'success': {fn:function(r) {
                    this.getSelectionModel().clearSelections(true);
                    this.refresh();
                },scope:this}
            }
        });

        return true;
    }

    ,inactiveSelected: function(btn,e) {
        var cs = this.getSelectedAsList();
        if (cs === false) return false;

        MODx.msg.confirm({
            title: _('msoptionscolor_menu_inactive')
            ,text: _('msoptionscolor_menu_inactive_multiple_confirm')
            ,url: msoptionscolor.config.connector_url
            ,params: {
                action: 'mgr/color/active_multiple'
                ,ids: cs
                ,value: 0
            }
            ,listeners: {
                'success': {fn:function(r) {
                    this.getSelectionModel().clearSelections(true);
                    this.refresh();
                },scope:this}
            }
        });

        return true;
    }

});
Ext.reg('msoptionscolor-product-color-grid',msoptionscolor.grid.Color);


msoptionscolor.window.CreateColor = function(config) {
    config = config || {};
    this.ident = config.ident || 'meuitem'+Ext.id();
    Ext.applyIf(config,{
        title: _('msoptionscolor_menu_create')
        ,id: this.ident
        ,width: 600
        ,autoHeight: true
        ,labelAlign: 'left'
        ,labelWidth: 180
        ,url: msoptionscolor.config.connector_url
        ,action: 'mgr/color/create'
        ,fields: config.fields
        ,keys: [{key: Ext.EventObject.ENTER,shift: true,fn: function() {this.submit() },scope: this}]
    });
    msoptionscolor.window.CreateColor.superclass.constructor.call(this,config);
};
Ext.extend(msoptionscolor.window.CreateColor,MODx.Window);
Ext.reg('msoptionscolor-window-color-create',msoptionscolor.window.CreateColor);


msoptionscolor.window.UpdateColor = function(config) {
    config = config || {};
    this.ident = config.ident || 'meuitem'+Ext.id();
    Ext.applyIf(config,{
        title: _('msoptionscolor_menu_update')
        ,id: this.ident
        ,width: 600
        ,autoHeight: true
        ,labelAlign: 'left'
        ,labelWidth: 180
        ,url: msoptionscolor.config.connector_url
        ,action: 'mgr/color/update'
        ,fields: config.fields
        ,keys: [{key: Ext.EventObject.ENTER,shift: true,fn: function() {this.submit() },scope: this}]
    });
    msoptionscolor.window.UpdateColor.superclass.constructor.call(this,config);
};
Ext.extend(msoptionscolor.window.UpdateColor,MODx.Window);
Ext.reg('msoptionscolor-window-color-update',msoptionscolor.window.UpdateColor);
