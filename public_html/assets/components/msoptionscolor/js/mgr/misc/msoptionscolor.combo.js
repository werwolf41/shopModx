Ext.namespace('msoptionscolor.combo');

msoptionscolor.combo.ProductKey = function(config) {
    config = config || {};

    Ext.applyIf(config,{
        name: 'property'
        ,id: 'msoptionscolor-combo-product-key'
        ,hiddenName: 'key'
        ,displayField: 'name'
        ,valueField: 'id'
        ,editable: true
        ,fields: ['id','name']
        ,pageSize: 10
        ,emptyText: _('msoptionscolor_combo_select')
        ,hideMode: 'offsets'
        ,url: msoptionscolor.config.connector_url
        ,baseParams: {
            action: 'mgr/misc/product/key/getlist'
        }
    });
    msoptionscolor.combo.ProductKey.superclass.constructor.call(this,config);
};
Ext.extend(msoptionscolor.combo.ProductKey,MODx.combo.ComboBox);
Ext.reg('msoptionscolor-combo-product-key',msoptionscolor.combo.ProductKey);


msoptionscolor.combo.Option = function(config) {
    config = config || {};

    Ext.applyIf(config,{
        name: 'option'
        ,id: 'msoptionscolor-combo-option'
        ,hiddenName: 'option'
        ,displayField: 'name'
        ,valueField: 'id'
        ,valueHiddenField:'key'
        ,editable: true
        ,fields: ['id','name','description','key']
        ,pageSize: 10
        ,emptyText: _('msoptionscolor_filter_select')
        ,hideMode: 'offsets'
        ,url: msoptionscolor.config.connector_url
        ,baseParams: {
            action: 'mgr/misc/option/getlist'
            ,addall: config.addall || 0
        }
        ,tpl: new Ext.XTemplate(''
        +'<tpl for="."><div class="x-combo-list-item msoptionscolor-option-list-item">'
            +'<span><b>{name}</b></span>'
                +'<small> [{key}] </small>'
                +'<tpl if="description">'
                +'<span class="description">'
                +'<nobr><small>{description}</small></nobr>'
            +'</span>'
        +'</tpl>'

        +'</div></tpl>',{
            compiled: true
        })
        ,itemSelector: 'div.msoptionscolor-option-list-item'
    });

    msoptionscolor.combo.Option.superclass.constructor.call(this,config);
};
Ext.extend(msoptionscolor.combo.Option,MODx.combo.ComboBox);
Ext.reg('msoptionscolor-combo-option',msoptionscolor.combo.Option);

msoptionscolor.combo.minishop2OneOptions = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        xtype:'superboxselect'

        ,allowBlank: false
        ,allowAddNewData: false
        ,addNewDataOnBlur : false
        ,forceSameValueQuery : true
        ,editable: false

        ,msgTarget: 'under'
        ,resizable: true
        ,forceFormValue: false

        ,name: config.name || 'tags'
        ,anchor:'100%'
        ,minChars: 2
		,pageSize: 10

        ,store:new Ext.data.JsonStore({
            id: (config.name || 'tags') + '-msbe-minishop2-options'
            ,root:'results'
            ,autoLoad: true
            ,autoSave: false
            ,totalProperty:'total'
            ,fields:['value']
            ,url: msoptionscolor.config.connector_url
            ,baseParams: {
                action: 'mgr/misc/product/options/getoptions',
                option_id: config.option_id,
                product_id: config.product_id
				//limit: 2
            }
        })
        ,mode: 'remote'
        ,displayField: 'value'
        ,valueField: 'value'
        ,triggerAction: 'all'
        ,extraItemCls: 'x-tag'
        ,originalName: config.name
        ,expandBtnCls: 'x-form-trigger'
        ,clearBtnCls: 'x-form-trigger'
        ,listeners: {
            beforeadditem: function () {
                this.removeAllItems();
            }
        }
    });
    msoptionscolor.combo.minishop2OneOptions.superclass.constructor.call(this,config);
};
Ext.extend(msoptionscolor.combo.minishop2OneOptions,Ext.ux.form.SuperBoxSelect);
Ext.reg('msoptionscolor-minishop2-combo-one-options',msoptionscolor.combo.minishop2OneOptions);


msoptionscolor.combo.Browser = function(config) {
    config = config || {};

    if (config.length != 0 && typeof config.openTo !== "undefined") {
        if (!/^\//.test(config.openTo)) {
            config.openTo = '/' + config.openTo;
        }
        if (!/$\//.test(config.openTo)) {
            var tmp = config.openTo.split('/')
            delete tmp[tmp.length - 1];
            tmp = tmp.join('/');
            config.openTo = tmp.substr(1)
        }
    }

    Ext.applyIf(config,{
        width: 300
        ,triggerAction: 'all'
    });
    msoptionscolor.combo.Browser.superclass.constructor.call(this,config);
    this.config = config;

};
Ext.extend(msoptionscolor.combo.Browser,Ext.form.TriggerField,{
    browser: null

	,onRender: function(ct, position){
		this.doc = Ext.isIE ? Ext.getBody() : Ext.getDoc();
		Ext.form.TriggerField.superclass.onRender.call(this, ct, position);

		this.wrap = this.el.wrap({
			cls: 'x-form-field-wrap x-form-field-trigger-wrap'
		});
		this.trigger = this.wrap.createChild(this.triggerConfig ||
		{tag: 'div', cls: 'x-form-trigger ' + (this.triggerClass || '')});
		this.initTrigger();
		if(!this.width){
			this.wrap.setWidth(this.el.getWidth()+this.trigger.getWidth());
		}
		this.resizeEl = this.positionEl = this.wrap;


	}

    ,onTriggerClick : function(btn){
        if (this.disabled){
            return false;
        }

        //if (this.browser === null) {
        this.browser = MODx.load({
            xtype: 'modx-browser'
            ,id: Ext.id()
            ,multiple: true
            ,source: this.config.source || MODx.config.default_media_source
            ,rootVisible: this.config.rootVisible || false
            ,allowedFileTypes: this.config.allowedFileTypes || ''
            ,wctx: this.config.wctx || 'web'
            ,openTo: this.config.openTo || ''
            ,rootId: this.config.rootId || '/'
            ,hideSourceCombo: this.config.hideSourceCombo || false
            ,hideFiles: this.config.hideFiles || true
            ,listeners: {
                'select': {fn: function(data) {
                    this.setValue(data.fullRelativeUrl);
                    this.fireEvent('select',data);

					var i = Ext.get(this.config.id);
					if (i) {
						i.applyStyles('background-image: url(/' + data.fullRelativeUrl + ');background-repeat:repeat-x;');
					}

                },scope:this}
            }
        });
        //}
        this.browser.win.buttons[0].on('disable',function(e) {this.enable()})
        this.browser.win.tree.on('click', function(n,e) {
                path = this.getPath(n);
                this.setValue(path);
            },this
        );
        this.browser.win.tree.on('dblclick', function(n,e) {
                path = this.getPath(n);
                this.setValue(path);
                this.browser.hide()
            },this
        );
        this.browser.show(btn);
        return true;
    }
    ,onDestroy: function(){
        msoptionscolor.combo.Browser.superclass.onDestroy.call(this);
    }
    ,getPath: function(n) {
        if (n.id == '/') {return '';}
        data = n.attributes;
        path = data.path + '/';

        return path;
    }
});
Ext.reg('msoptionscolor-combo-browser',msoptionscolor.combo.Browser);


/*---------------RAL---------------------*/
msoptionscolor.combo.Ral = function(config) {
    config = config || {};

    Ext.applyIf(config,{
        name: 'option'
        ,id: 'msoptionscolor-combo-ral'
        ,hiddenName: 'ral'
        ,displayField: 'ral'
        ,valueField: 'ral'
        ,valueHiddenField:'ral'
        ,editable: true
        ,fields: ['id','ral','description','html']
        ,pageSize: 10
        ,emptyText: _('msoptionscolor_filter_select')
        ,hideMode: 'offsets'
        ,url: msoptionscolor.config.connector_url
        ,baseParams: {
            action: 'mgr/misc/ral/getlist'
            ,addall: config.addall || 0
        }
        ,tpl: new Ext.XTemplate(''
        +'<tpl for="."><div class="x-combo-list-item msoptionscolor-ral-list-item">'
        +'<small style="background:#{html};padding: 2px;width: 50px!important;display: inline-block;text-align: center;border-radius: 2px;margin-right: 10px;"> {html} </small>'
        +'<span><b>{ral}</b></span>'
        +'<tpl if="description">'
        +'<span class="description">'
        +'<nobr><small>{description}</small></nobr>'
        +'</span>'
        +'</tpl>'

        +'</div></tpl>',{
            compiled: true
        })
        ,itemSelector: 'div.msoptionscolor-ral-list-item'
    });

    msoptionscolor.combo.Ral.superclass.constructor.call(this,config);
};
Ext.extend(msoptionscolor.combo.Ral,MODx.combo.ComboBox);
Ext.reg('msoptionscolor-combo-ral',msoptionscolor.combo.Ral);


msoptionscolor.combo.Rals = function(config) {
    config = config || {};

    Ext.applyIf(config, {
        xtype: 'superboxselect',
        name: config.name || 'rals',
        anchor: '100%',
        msgTarget: 'under',
        allowAddNewData: true,
        addNewDataOnBlur: true,
        resizable: true,
        allowBlank: true,
        displayField: 'value',
        valueField: 'value',

        store: new Ext.data.JsonStore({
            id: (config.name || 'rals') + '-store',
            root: 'results',
            autoLoad: true,
            autoSave: false,
            totalProperty: 'total',
            fields: ['id', 'ral', 'description', 'html', 'value'],
            url: msoptionscolor.config.connector_url,
            baseParams: {
                action: 'mgr/misc/ral/getlist',
                addall: config.addall || 0,
                key: config.key || 'value',
                combo: 1
            },
            listeners: {
                beforeload: function(store, operation) {
                }
            }
        }),

        tpl: new Ext.XTemplate(
            '<tpl for="."><div class="x-combo-list-item msoptionscolor-ral-list-item">',
            '<small style="background:#{html};padding: 2px;width: 50px!important;display: inline-block;text-align: center;border-radius: 2px;margin-right: 10px;"> {html} </small>',
            '<span><b>{ral}</b></span>',
            '</div></tpl>', {
            compiled: true
        }),
        itemSelector: 'div.msoptionscolor-ral-list-item',

        mode: 'remote',
        triggerAction: 'all',
        extraItemCls: 'x-tag',
        expandBtnCls:'x-form-trigger',
        clearBtnCls: 'x-form-trigger',
        listeners: {
            select: { fn: MODx.fireResourceFormChange, scope: this },
            beforeadditem: { fn: MODx.fireResourceFormChange, scope: this },
            beforeremoveitem: { fn: MODx.fireResourceFormChange, scope: this },
            clear: { fn: MODx.fireResourceFormChange, scope: this }
        }
    });
    config.name += '[]';
    msoptionscolor.combo.Rals.superclass.constructor.call(this, config);
};
Ext.extend(msoptionscolor.combo.Rals, Ext.ux.form.SuperBoxSelect, {

});
Ext.reg('msoptionscolor-combo-rals', msoptionscolor.combo.Rals);

