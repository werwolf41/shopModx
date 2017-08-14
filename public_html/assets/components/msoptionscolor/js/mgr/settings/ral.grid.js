msoptionscolor.grid.Ral = function(config) {
    config = config || {};

    this.exp = new Ext.grid.RowExpander({
        expandOnDblClick: false
        ,tpl : new Ext.Template('<p class="desc">{description}</p>')
        ,renderer : function(v, p, record){return record.data.description != '' && record.data.description != null ? '<div class="x-grid3-row-expander">&#160;</div>' : '&#160;';}
    });
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
                            action: config.action || 'mgr/settings/ral/sort'
                            ,source: source
                            ,target: target
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
    Ext.applyIf(config,{
        id: 'msoptionscolor-grid-product-ral'
        ,url: msoptionscolor.config.connector_url
        ,baseParams: {
            action: 'mgr/settings/ral/getlist'
        }
        ,fields: ['id','name','ral','html','description','active','editable']
        ,autoHeight: true
        ,paging: true
        ,remoteSort: true
        ,save_action: 'mgr/settings/ral/updatefromgrid'
        ,autosave: true
        ,plugins: this.exp
        ,columns: [this.exp
            ,{header: _('msoptionscolor_id'),dataIndex: 'id',width: 50, sortable: true}
            ,{header: _('msoptionscolor_ral'),dataIndex: 'ral',width: 100, sortable: true /*editor:{xtype:'textfield'}*/}
            ,{header: _('msoptionscolor_html'),dataIndex: 'html',width: 100, sortable: true,renderer: msoptionscolor.utils.renderColor /*editor:{xtype:'textfield'}*/}
            ,{header: _('msoptionscolor_name'),dataIndex: 'name',width: 100, sortable: true /*editor:{xtype:'textfield'}*/}
            ,{header: _('msoptionscolor_active'),dataIndex: 'active',sortable:true, width:50, renderer: msoptionscolor.utils.renderBoolean/*editor:{xtype:'combo-boolean', renderer:'boolean'}*/}

        ]
        ,tbar: [
            {
                text: '<i class="icon icon-plus"></i> '+_('msoptionscolor_btn_create')
                ,handler: this.createRal
                ,scope: this
            },'->',{
                xtype: 'textfield'
                ,name: 'query'
                ,width: 200
                ,id: 'msoptionscolor-ral-search'
                ,emptyText: _('msoptionscolor_search')
                ,listeners: {
                    render: {fn:function(tf) {tf.getEl().addKeyListener(Ext.EventObject.ENTER,function() {this.FilterByQuery(tf);},this);},scope:this}
                }
            },{
                xtype: 'button'
                ,id: 'msoptionscolor-ral-clear'
                ,text: '<i class="icon icon-times"></i>'
                ,listeners: {
                    click: {fn: this.clearFilter, scope: this}
                }
            }
        ]
        ,ddGroup: 'dd'
        ,enableDragDrop: true
        ,listeners: {
            rowDblClick: function(grid, rowIndex, e) {
                var row = grid.store.getAt(rowIndex);
                this.updateRal(grid, e, row);
            }
            ,render: {fn: this.dd, scope: this}
        }
    });
    msoptionscolor.grid.Ral.superclass.constructor.call(this,config);
};
Ext.extend(msoptionscolor.grid.Ral,MODx.grid.Grid,{
    windows: {}

    ,FilterByQuery: function(tf, nv, ov) {
        var s = this.getStore();
        s.baseParams.query = tf.getValue();
        this.getBottomToolbar().changePage(1);
        this.refresh();
    }

    ,clearFilter: function(btn,e) {
        var s = this.getStore();
        s.baseParams.query = '';
        Ext.getCmp('msoptionscolor-ral-search').setValue('');
        this.getBottomToolbar().changePage(1);
        this.refresh();
    }

    ,getMenu: function() {
        var m = [];
        m.push({
            text: _('msoptionscolor_menu_update')
            ,handler: this.updateRal
        });
        if (this.menu.record.editable) {
            m.push('-');
            m.push({
                text: _('msoptionscolor_menu_remove')
                ,handler: this.removeRal
            });
        }
        this.addContextMenuItem(m);
    }

    ,createRal: function(btn,e) {
        if (this.windows.createRal) {
            this.windows.createRal.close();
            this.windows.createRal.destroy();
            this.windows.createRal = undefined;
        }
        if (!this.windows.createRal) {
            this.windows.createRal = MODx.load({
                xtype: 'msoptionscolor-window-ral-create'
                ,id: 'msoptionscolor-window-ral-create'
                ,fields: this.getRalFields('create')
                ,listeners: {
                    success: {fn:function() { this.refresh(); },scope:this}
                }
            });
        }
        this.windows.createRal.fp.getForm().reset();
        this.windows.createRal.fp.getForm().setValues({
            active: 1
        });
        this.windows.createRal.show(e.target);
    }


    ,updateRal: function(btn,e,row) {
        if (typeof(row) != 'undefined') {this.menu.record = row.data;}
        var id = this.menu.record.id;

        MODx.Ajax.request({
            url: msoptionscolor.config.connector_url
            ,params: {
                action: 'mgr/settings/ral/get'
                ,id: id
            }
            ,listeners: {
                success: {fn:function(r) {

                    if (this.windows.updateRal) {
                        try {
                            this.windows.updateRal.close();
                            this.windows.updateRal.destroy();
                        } catch (e) {}
                    }
                    this.windows.updateRal = MODx.load({
                        xtype: 'msoptionscolor-window-ral-update'
                        ,record:r.object
                        ,fields: this.getRalFields('update')
                        ,listeners: {
                            success: {fn:function() {this.refresh();},scope:this}
                        }
                    });
                    this.windows.updateRal.fp.getForm().reset();
                    this.windows.updateRal.show(e.target);
                    this.windows.updateRal.fp.getForm().setValues(r.object);
                },scope:this}
            }
        });
    }

    ,removeRal: function(btn,e) {
        if (!this.menu.record) return false;

        MODx.msg.confirm({
            title: _('msoptionscolor_menu_remove') + '"' + this.menu.record.name + '"'
            ,text: _('msoptionscolor_menu_remove_confirm')
            ,url: this.config.url
            ,params: {
                action: 'mgr/settings/ral/remove'
                ,id: this.menu.record.id
            }
            ,listeners: {
                success: {fn:function(r) {this.refresh();}, scope:this}
            }
        });
    }

    ,getRalFields: function(type) {

        var fields = [];
        var disabled = type == 'update' ? true : false;

        fields.push(
            {xtype: 'hidden',name: 'id', id: 'msoptionscolor-product-ral-id-'+type}
            ,{xtype: 'textfield', fieldLabel: _('msoptionscolor_ral'), name: 'ral', hiddenName: 'ral', allowBlank: false, anchor: '99%', id: 'msoptionscolor-product-ral-name-ral-'+type}
            ,{xtype: 'colorpickerfield', fieldLabel: _('msoptionscolor_html'), name: 'html', hiddenName: 'html', allowBlank: false, anchor: '99%', id: 'msoptionscolor-product-ral-name-html-'+type}
            ,{xtype: 'textfield', fieldLabel: _('msoptionscolor_name'), name: 'name', hiddenName: 'name', allowBlank: true, anchor: '99%', id: 'msoptionscolor-product-ral-name-name-'+type}
            ,{xtype: 'textarea', fieldLabel: _('msoptionscolor_description'), name: 'description', anchor: '99%', id: 'msoptionscolor-product-ral-description-'+type}

            ,{xtype: 'checkboxgroup'
                ,columns: 2
                ,items: [
                    {xtype: 'xcheckbox', boxLabel: _('msoptionscolor_active'), name: 'active', id: 'msoptionscolor-product-ral-active-'+type}
                ]
                ,id: 'msoptionscolor-product-ral-group-'+type
            }

        );

        return fields;
    }

});
Ext.reg('msoptionscolor-grid-setting-ral',msoptionscolor.grid.Ral);


msoptionscolor.window.CreateRal = function(config) {
    config = config || {};
    this.ident = config.ident || 'mecitem'+Ext.id();
    Ext.applyIf(config,{
        title: _('msoptionscolor_menu_create')
        ,id: this.ident
        ,width: 600
        ,autoHeight: true
        ,labelAlign: 'left'
        ,labelWidth: 180
        ,url: msoptionscolor.config.connector_url
        ,action: 'mgr/settings/ral/create'
        ,fields: config.fields
        ,keys: [{key: Ext.EventObject.ENTER,shift: true,fn: function() {this.submit() },scope: this}]
    });
    msoptionscolor.window.CreateRal.superclass.constructor.call(this,config);
};
Ext.extend(msoptionscolor.window.CreateRal,MODx.Window);
Ext.reg('msoptionscolor-window-ral-create',msoptionscolor.window.CreateRal);


msoptionscolor.window.UpdateRal = function(config) {
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
        ,action: 'mgr/settings/ral/update'
        ,fields: config.fields
        ,keys: [{key: Ext.EventObject.ENTER,shift: true,fn: function() {this.submit() },scope: this}]
    });
    msoptionscolor.window.UpdateRal.superclass.constructor.call(this,config);
};
Ext.extend(msoptionscolor.window.UpdateRal,MODx.Window);
Ext.reg('msoptionscolor-window-ral-update',msoptionscolor.window.UpdateRal);