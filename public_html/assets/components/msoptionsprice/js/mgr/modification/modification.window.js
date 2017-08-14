msoptionsprice.window.modification = function (config) {
	config = config || {record: {}};

	if (!config.update) {
		config.update = true;
	}

	Ext.applyIf(config, {
		title: _('create'),
		width: 650,
		autoHeight: true,
		url: msoptionsprice.config.connector_url,
		action: 'mgr/modification/update',
		fields: this.getFields(config),
		keys: this.getKeys(config),
		listeners: this.getListeners(config),
		cls: 'msoptionsprice-panel-modification',
	});

	msoptionsprice.window.modification.superclass.constructor.call(this, config);
};
Ext.extend(msoptionsprice.window.modification, MODx.Window, {

	getKeys: function (config) {
		return [{
			key: Ext.EventObject.ENTER, shift: true, fn: function () {
				this.submit()
			}, scope: this
		}];
	},

	getFields: function (config) {
		return [{
			layout: 'form',
			defaults: {border: false, anchor: '100%'},
			items: [{
				xtype: 'hidden',
				name: 'id'
			}, {
				xtype: 'hidden',
				name: 'rid'
			}, {
				layout: 'column',
				border: false,
				items: [{
					columnWidth: .33,
					layout: 'form',
					defaults: {border: false, anchor: '100%'},
					items: [{
						layout: 'column',
						border: false,
						items: [{
							columnWidth: .3,
							layout: 'form',
							defaults: {border: false, anchor: '100%'},
							items: [{
								xtype: 'msoptionsprice-combo-modification-type',
								fieldLabel: _('msoptionsprice_type'),
								name: 'type',
								allowBlank: false,
								listeners: {
									afterrender: {
										fn: function (r) {
											this.handleChangeType(0);
										},
										scope: this
									},
									select: {
										fn: function (r) {
											this.handleChangeType(1);
										},
										scope: this
									}
								}
							}]
						}, {
							columnWidth: .7,
							layout: 'form',
							defaults: {border: false, anchor: '100%'},
							items: [{
								xtype: 'textfield',
								fieldLabel: _('msoptionsprice_price'),
								name: 'price',
								maskRe: /[0123456789\.\-]/,
								allowBlank: false
							}]
						}]
					}, {
						xtype: 'msoptionsprice-combo-product-image',
						fieldLabel: _('msoptionsprice_image'),
						name: 'image',
						rid: config.record.rid,
						custm: true,
						clear: true,
						allowBlank: true
					}]
				}, {
					columnWidth: .33,
					layout: 'form',
					defaults: {border: false, anchor: '100%'},
					items: [{
						xtype: 'textfield',
						fieldLabel: _('msoptionsprice_article'),
						name: 'article',
						allowBlank: true
					}, {

					}]
				}, {
					columnWidth: .33,
					layout: 'form',
					defaults: {border: false, anchor: '100%'},
					items: [{
						xtype: 'numberfield',
						decimalPrecision: 3,
						fieldLabel: _('msoptionsprice_weight'),
						name: 'weight',
						allowBlank: true
					}, {
						xtype: 'numberfield',
						decimalPrecision: 0,
						fieldLabel: _('msoptionsprice_count'),
						name: 'count',
						allowBlank: true
					}]
				}]
			}, {
				xtype: 'msoptionsprice-grid-option',
				record: config.record
			}, {
				xtype: 'checkboxgroup',
				fieldLabel: '',
				hideLabel: true,
				columns: 2,
				items: [{
					xtype: 'xcheckbox',
					boxLabel: _('msoptionsprice_active'),
					name: 'active',
					checked: config.record.active
				}]
			}]
		}];

	},

	getListeners: function (config) {
		return Ext.applyIf(config.listeners, {
			beforeSubmit: {
				fn: function () {
					//this.saveField();
				}, scope: this
			}
		});
	},

	handleChangeType: function (change) {
		var f = this.fp.getForm();
		var _type = f.findField('type');
		var _price = f.findField('price');

		switch (_type.getValue()) {
			case 1:
				_price.maskRe = /[0123456789\.\-]/;
				break;
			case 2:
			case 3:
				_price.maskRe = /[0123456789\.\-%]/;
				break;
		}
	},

	loadDropZones: function () {

	}

});
Ext.reg('msoptionsprice-window-modification', msoptionsprice.window.modification);
