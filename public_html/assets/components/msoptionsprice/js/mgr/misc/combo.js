Ext.namespace('msoptionsprice.combo');


msoptionsprice.combo.optionKey = function (config) {
	config = config || {};

	if (config.custm) {
		config.triggerConfig = [{
			tag: 'div',
			cls: 'x-field-search-btns',
			style: String.format('width: {0}px;', config.clear ? 62 : 31),
			cn: [{
				tag: 'div',
				cls: 'x-form-trigger x-field-msoptionsprice-option-key-go'
			}]
		}];
		if (config.clear) {
			config.triggerConfig[0].cn.push({
				tag: 'div',
				cls: 'x-form-trigger x-field-msoptionsprice-option-key-clear'
			});
		}
		config.initTrigger = function () {
			var ts = this.trigger.select('.x-form-trigger', true);
			this.wrap.setStyle('overflow', 'hidden');
			var triggerField = this;
			ts.each(function (t, all, index) {
				t.hide = function () {
					var w = triggerField.wrap.getWidth();
					this.dom.style.display = 'none';
					triggerField.el.setWidth(w - triggerField.trigger.getWidth());
				};
				t.show = function () {
					var w = triggerField.wrap.getWidth();
					this.dom.style.display = '';
					triggerField.el.setWidth(w - triggerField.trigger.getWidth());
				};
				var triggerIndex = 'Trigger' + (index + 1);

				if (this['hide' + triggerIndex]) {
					t.dom.style.display = 'none';
				}
				t.on('click', this['on' + triggerIndex + 'Click'], this, {
					preventDefault: true
				});
				t.addClassOnOver('x-form-trigger-over');
				t.addClassOnClick('x-form-trigger-click');
			}, this);
			this.triggers = ts.elements;
		};
	}
	Ext.applyIf(config, {
		name: config.name || 'key',
		hiddenName: config.name || 'key',
		displayField: 'key',
		valueField: 'key',
		editable: true,
		fields: ['key', 'caption'],
		pageSize: 10,
		emptyText: _('msoptionsprice_combo_select'),
		hideMode: 'offsets',
		url: msoptionsprice.config.connector_url,
		baseParams: {
			action: 'mgr/misc/option/getkeys',
			combo: true,
			iskey: true,
			rid: config.rid || 0,
		},
		tpl: new Ext.XTemplate(
			'<tpl for="."><div class="x-combo-list-item">',
			'<b>{key}</b>',
			'<br/><small>{caption:this.renderCaption}</small>',
			'</div></tpl>',
			{
				compiled: true,
				renderCaption: function (value, record) {
					var title = value || record['key'];
					title = _('msoptionsprice_' + title) || _('ms2_product_' + title) || title;

					return title;
				}
			}),
		cls: 'input-combo-msoptionsprice-option-key',
		clearValue: function () {
			if (this.hiddenField) {
				this.hiddenField.value = '';
			}
			this.setRawValue('');
			this.lastSelectionText = '';
			this.applyEmptyText();
			this.value = '';
			this.fireEvent('select', this, null, 0);
			this.getStore().reload();

			if (!!this.pageTb) {
				this.pageTb.show();
			}
		},

		getTrigger: function (index) {
			return this.triggers[index];
		},

		onTrigger1Click: function () {
			this.onTriggerClick();
		},

		onTrigger2Click: function () {
			this.clearValue();
		}
	});
	msoptionsprice.combo.optionKey.superclass.constructor.call(this, config);
};
Ext.extend(msoptionsprice.combo.optionKey, MODx.combo.ComboBox);
Ext.reg('msoptionsprice-combo-option-key', msoptionsprice.combo.optionKey);


msoptionsprice.combo.optionValues = function (config) {
	config = config || {};

	if (config.custm) {
		config.triggerConfig = [{
			tag: 'div',
			cls: 'x-field-search-btns',
			style: String.format('width: {0}px;', config.clear ? 62 : 31),
			cn: [{
				tag: 'div',
				cls: 'x-form-trigger x-field-msoptionsprice-option-values-go'
			}]
		}];
		if (config.clear) {
			config.triggerConfig[0].cn.push({
				tag: 'div',
				cls: 'x-form-trigger x-field-msoptionsprice-option-values-clear'
			});
		}
		config.initTrigger = function () {
			var ts = this.trigger.select('.x-form-trigger', true);
			this.wrap.setStyle('overflow', 'hidden');
			var triggerField = this;
			ts.each(function (t, all, index) {
				t.hide = function () {
					var w = triggerField.wrap.getWidth();
					this.dom.style.display = 'none';
					triggerField.el.setWidth(w - triggerField.trigger.getWidth());
				};
				t.show = function () {
					var w = triggerField.wrap.getWidth();
					this.dom.style.display = '';
					triggerField.el.setWidth(w - triggerField.trigger.getWidth());
				};
				var triggerIndex = 'Trigger' + (index + 1);

				if (this['hide' + triggerIndex]) {
					t.dom.style.display = 'none';
				}
				t.on('click', this['on' + triggerIndex + 'Click'], this, {
					preventDefault: true
				});
				t.addClassOnOver('x-form-trigger-over');
				t.addClassOnClick('x-form-trigger-click');
			}, this);
			this.triggers = ts.elements;
		};
	}
	Ext.applyIf(config, {
		name: config.name || 'value',
		hiddenName: config.name || 'value',
		displayField: 'value',
		valueField: 'value',
		editable: true,//false,
		minChars: 1,
		fields: ['value'],
		pageSize: 10,
		emptyText: _('msoptionsprice_combo_select'),
		hideMode: 'offsets',
		url: msoptionsprice.config.connector_url,
		baseParams: {
			action: 'mgr/misc/option/getvalues',
			combo: true,
			rid: config.rid || '',
			key: config.key || '',
		},
		tpl: new Ext.XTemplate(
			'<tpl for="."><div class="x-combo-list-item">',
			'<b>{value}</b>',
			'</div></tpl>',
			{
				compiled: true
			}),
		cls: 'input-combo-msoptionsprice-option-values',
		clearValue: function () {
			if (this.hiddenField) {
				this.hiddenField.value = '';
			}
			this.setRawValue('');
			this.lastSelectionText = '';
			this.applyEmptyText();
			this.value = '';
			this.fireEvent('select', this, null, 0);
			this.getStore().reload();

			if (!!this.pageTb) {
				this.pageTb.show();
			}
		},

		getTrigger: function (index) {
			return this.triggers[index];
		},

		onTrigger1Click: function () {
			this.onTriggerClick();
		},

		onTrigger2Click: function () {
			this.clearValue();
		}
	});
	msoptionsprice.combo.optionValues.superclass.constructor.call(this, config);
};
Ext.extend(msoptionsprice.combo.optionValues, MODx.combo.ComboBox);
Ext.reg('msoptionsprice-combo-option-values', msoptionsprice.combo.optionValues);


msoptionsprice.combo.modificationType = function (config) {
	config = config || {};

	if (config.custm) {
		config.triggerConfig = [{
			tag: 'div',
			cls: 'x-field-search-btns',
			style: String.format('width: {0}px;', config.clear ? 62 : 31),
			cn: [{
				tag: 'div',
				cls: 'x-form-trigger x-field-msoptionsprice-modification-type-go'
			}]
		}];
		if (config.clear) {
			config.triggerConfig[0].cn.push({
				tag: 'div',
				cls: 'x-form-trigger x-field-msoptionsprice-modification-type-clear'
			});
		}
		config.initTrigger = function () {
			var ts = this.trigger.select('.x-form-trigger', true);
			this.wrap.setStyle('overflow', 'hidden');
			var triggerField = this;
			ts.each(function (t, all, index) {
				t.hide = function () {
					var w = triggerField.wrap.getWidth();
					this.dom.style.display = 'none';
					triggerField.el.setWidth(w - triggerField.trigger.getWidth());
				};
				t.show = function () {
					var w = triggerField.wrap.getWidth();
					this.dom.style.display = '';
					triggerField.el.setWidth(w - triggerField.trigger.getWidth());
				};
				var triggerIndex = 'Trigger' + (index + 1);

				if (this['hide' + triggerIndex]) {
					t.dom.style.display = 'none';
				}
				t.on('click', this['on' + triggerIndex + 'Click'], this, {
					preventDefault: true
				});
				t.addClassOnOver('x-form-trigger-over');
				t.addClassOnClick('x-form-trigger-click');
			}, this);
			this.triggers = ts.elements;
		};
	}
	Ext.applyIf(config, {
		name: config.name || 'type',
		hiddenName: config.name || 'type',
		displayField: 'name',
		valueField: 'id',
		editable: false,
		fields: ['id', 'name', 'description'],
		pageSize: 10,
		emptyText: _('msoptionsprice_combo_select'),
		hideMode: 'offsets',
		url: msoptionsprice.config.connector_url,
		baseParams: {
			action: 'mgr/misc/modification/gettypes',
			combo: true,
		},
		tpl: new Ext.XTemplate(
			'<tpl for="."><div class="x-combo-list-item" ext:qtip="{description}">',
			'<b>{name}</b>',
			'</div></tpl>',
			{
				compiled: true
			}),
		cls: 'input-combo-msoptionsprice-modification-type',
		clearValue: function () {
			if (this.hiddenField) {
				this.hiddenField.value = '';
			}
			this.setRawValue('');
			this.lastSelectionText = '';
			this.applyEmptyText();
			this.value = '';
			this.fireEvent('select', this, null, 0);
			this.getStore().reload();

			if (!!this.pageTb) {
				this.pageTb.show();
			}
		},

		getTrigger: function (index) {
			return this.triggers[index];
		},

		onTrigger1Click: function () {
			this.onTriggerClick();
		},

		onTrigger2Click: function () {
			this.clearValue();
		}
	});
	msoptionsprice.combo.modificationType.superclass.constructor.call(this, config);
};
Ext.extend(msoptionsprice.combo.modificationType, MODx.combo.ComboBox);
Ext.reg('msoptionsprice-combo-modification-type', msoptionsprice.combo.modificationType);


msoptionsprice.combo.productImage = function (config) {
	config = config || {};

	if (config.custm) {
		config.triggerConfig = [{
			tag: 'div',
			cls: 'x-field-search-btns',
			style: String.format('width: {0}px;', config.clear ? 62 : 31),
			cn: [{
				tag: 'div',
				cls: 'x-form-trigger x-field-msoptionsprice-product-image-go'
			}]
		}];
		if (config.clear) {
			config.triggerConfig[0].cn.push({
				tag: 'div',
				cls: 'x-form-trigger x-field-msoptionsprice-product-image-clear'
			});
		}
		config.initTrigger = function () {
			var ts = this.trigger.select('.x-form-trigger', true);
			this.wrap.setStyle('overflow', 'hidden');
			var triggerField = this;
			ts.each(function (t, all, index) {
				t.hide = function () {
					var w = triggerField.wrap.getWidth();
					this.dom.style.display = 'none';
					triggerField.el.setWidth(w - triggerField.trigger.getWidth());
				};
				t.show = function () {
					var w = triggerField.wrap.getWidth();
					this.dom.style.display = '';
					triggerField.el.setWidth(w - triggerField.trigger.getWidth());
				};
				var triggerIndex = 'Trigger' + (index + 1);

				if (this['hide' + triggerIndex]) {
					t.dom.style.display = 'none';
				}
				t.on('click', this['on' + triggerIndex + 'Click'], this, {
					preventDefault: true
				});
				t.addClassOnOver('x-form-trigger-over');
				t.addClassOnClick('x-form-trigger-click');
			}, this);
			this.triggers = ts.elements;
		};
	}
	Ext.applyIf(config, {
		name: config.name || 'type',
		hiddenName: config.name || 'type',
		displayField: 'name',
		valueField: 'id',
		editable: false,
		fields: ['id', 'name', 'thumbnail', 'url', 'description'],
		pageSize: 10,
		emptyText: _('msoptionsprice_combo_select'),
		hideMode: 'offsets',
		url: msoptionsprice.config.connector_url,
		baseParams: {
			action: 'mgr/misc/product/getimages',
			combo: true,
			rid: config.rid || 0,
			parent: config.parent || 0
		},

		tpl: new Ext.XTemplate(
			'<tpl for="."><div class="x-combo-list-item">',
			'<b>{name}</b>',
			'<tpl if="thumbnail">',
			'<div class="modx-pb-thumb msoptionsprice-thumb">',
			'<img src="{thumbnail}" ext:qtip="{url}" ext:qtitle="{name} {description}" ext:qclass="msoptionsprice-qtip"/>',
			'</div>',
			'</tpl>',
			'</div></tpl>',
			{
				compiled: true
			}),
		cls: 'input-combo-msoptionsprice-product-image',
		clearValue: function () {
			if (this.hiddenField) {
				this.hiddenField.value = '';
			}
			this.setRawValue('');
			this.lastSelectionText = '';
			this.applyEmptyText();
			this.value = '';
			this.fireEvent('select', this, null, 0);
			this.getStore().reload();

			if (!!this.pageTb) {
				this.pageTb.show();
			}
		},

		getTrigger: function (index) {
			return this.triggers[index];
		},

		onTrigger1Click: function () {
			this.pageTb.show();
			this.onTriggerClick();
		},

		onTrigger2Click: function () {
			this.pageTb.show();
			this.clearValue();
		}
	});
	msoptionsprice.combo.productImage.superclass.constructor.call(this, config);

};
Ext.extend(msoptionsprice.combo.productImage, MODx.combo.ComboBox);
Ext.reg('msoptionsprice-combo-product-image', msoptionsprice.combo.productImage);
