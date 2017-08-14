msoptionsprice.grid.modification = function (config) {
	config = config || {};

	this.exp = new Ext.grid.RowExpander({
		expandOnDblClick: false,
		enableCaching: false,
		tpl: new Ext.XTemplate(
			'<tpl for=".">',

			'<table class="msoptionsprice-expander"><tbody>',

			'<tpl if="values">',
			'<tr>',
			'<td>',
			'{values:this.renderValues}',
			'</td>',
			'</tr>',
			'</tpl>',

			'<tpl if="thumbnail">',
			'<tr class="modx-pb-thumb msoptionsprice-grid-thumb">',
			'<td>',
			'<img src="{thumbnail}" ext:qtip="{thumbnail}" ext:qtitle="{image_name}" ext:qclass="msoptionsprice-qtip"/>',
			'</td>',
			'</tr>',
			'</tpl>',

			' </tbody></table>',

			'</tpl>',
			{
				compiled: true,
				renderValues: function (value, record) {

					var values = [];
					var tmp = [];
					var pf = MODx.config['msoptionsprice_field_prefix'] || 'option.';
					var rx = new RegExp(pf.replace(".", ""));

					for (var i in record) {
						if (!record.hasOwnProperty(i)) {
							continue;
						}
						if (!rx.test(i)) {
							continue;
						}

						var name = i.split('.');
						name = name[1];

						switch (true) {
							case i == pf + name + '.key':
								var title = record[pf + name + '.caption'] || record[i];
								title = _('msoptionsprice_' + title) || _('ms2_product_' + title) || title;
								if (title != record[i]) {
									title = String.format('{0} </small>({1})</small>', title, record[i])
								}
								tmp.push({name: name, title: title});
								break;
							case i == pf + name && !!record[i] && typeof record[i] === 'object':
								tmp.push({name: name, value: record[i].join(', ')});
								break;
							case i == pf + name + '.value' && tmp.length == 1:
								tmp.push({name: name, value: record[i]});
								break;
						}

						if (tmp.length == 2 && tmp[0]['name'] == tmp[1]['name']) {
							if (tmp[0]['value'] && !!tmp[0]['value']) {
								values.push(String.format('<tr><td><b>{0}: </b>{1}</td></tr>', tmp[1]['title'], tmp[0]['value']));
							}
							else if (!!tmp[1]['value']) {
								values.push(String.format('<tr><td><b>{0}: </b>{1}</td></tr>', tmp[0]['title'], tmp[1]['value']));
							}
							tmp = [];
						}
					}

					return values.join('');
				}
			}
		)
	});

	this.exp.on('beforeexpand', function (rowexpander, record, body, rowIndex) {
		record['data']['json'] = record['json'];
		record['data'] = Ext.applyIf(record['data'], record['json']);
		return true;
	});

	this.dd = function (grid) {
		this.dropTarget = new Ext.dd.DropTarget(grid.container, {
			ddGroup: 'dd',
			copy: false,
			notifyDrop: function (dd, e, data) {
				var store = grid.store.data.items;
				var target = store[dd.getDragData(e).rowIndex].id;
				var source = store[data.rowIndex].id;
				if (target != source) {
					dd.el.mask(_('loading'), 'x-mask-loading');
					MODx.Ajax.request({
						url: msoptionsprice.config.connector_url,
						params: {
							action: config.action || 'mgr/modification/sort',
							source: source,
							target: target
						},
						listeners: {
							success: {
								fn: function (r) {
									dd.el.unmask();
									grid.refresh();
								},
								scope: grid
							},
							failure: {
								fn: function (r) {
									dd.el.unmask();
								},
								scope: grid
							}
						}
					});
				}
			}
		});
	};

	this.sm = new Ext.grid.CheckboxSelectionModel();

	Ext.applyIf(config, {
		id: 'msoptionsprice-grid-modification',
		url: msoptionsprice.config.connector_url,
		baseParams: {
			action: 'mgr/modification/getlist',
			rid: config.resource.id || 0,
			sort: 'rank',
			dir: 'asc'
		},
		save_action: 'mgr/modification/updatefromgrid',
		autosave: true,
		save_callback: this._updateRow,
		fields: this.getFields(config),
		columns: this.getColumns(config),
		tbar: this.getTopBar(config),
		listeners: this.getListeners(config),

		sm: this.sm,
		plugins: [this.exp],

		ddGroup: 'dd',
		enableDragDrop: true,

		paging: true,
		pageSize: 10,
		remoteSort: true,
		viewConfig: {
			forceFit: true,
			enableRowBody: true,
			autoFill: true,
			showPreview: true,
			scrollOffset: 0
		},
		autoHeight: true,
		cls: 'msoptionsprice-grid',
		bodyCssClass: 'grid-with-buttons',
		stateful: false,
	});
	msoptionsprice.grid.modification.superclass.constructor.call(this, config);
	this.exp.addEvents('beforeexpand', 'beforecollapse');

};
Ext.extend(msoptionsprice.grid.modification, MODx.grid.Grid, {
	windows: {},

	getFields: function (config) {
		var fields = msoptionsprice.config.grid_modification_fields;

		return fields;
	},

	getTopBar: function (config) {
		var tbar = [];

		var component = ['menu', 'update', 'left', 'search', 'bigspacer', 'spacer'];

		var add = {
			menu: {
				text: '<i class="icon icon-cogs"></i> ',
				menu: [{
					text: '<i class="icon icon-plus"></i> ' + _('msoptionsprice_action_create'),
					cls: 'msoptionsprice-cogs',
					handler: this.create,
					scope: this
				}, {
					text: '<i class="icon icon-trash-o red"></i> ' + _('msoptionsprice_action_remove'),
					cls: 'msoptionsprice-cogs',
					handler: this.remove,
					scope: this
				}, '-', {
					text: '<i class="icon icon-toggle-on green"></i> ' + _('msoptionsprice_action_turnon'),
					cls: 'msoptionsprice-cogs',
					handler: this.active,
					scope: this
				}, {
					text: '<i class="icon icon-toggle-off red"></i> ' + _('msoptionsprice_action_turnoff'),
					cls: 'msoptionsprice-cogs',
					handler: this.inactive,
					scope: this
				}]
			},
			update: {
				text: '<i class="icon icon-refresh"></i>',
				handler: this._updateRow,
				scope: this
			},
			left: '->',

			spacer: {
				xtype: 'spacer',
				style: 'width:1px;'
			},

			bigspacer: {
				xtype: 'spacer',
				style: 'width:5px;'
			}

		};

		component.filter(function (item) {
			if (add[item]) {
				tbar.push(add[item]);
			}
		});

		var items = [];
		if (tbar.length > 0) {
			items.push(new Ext.Toolbar(tbar));
		}

		return new Ext.Panel({items: items});
	},

	getColumns: function (config) {
		var columns = [this.exp, this.sm];
		var add = {
			id: {
				width: 5,
				hidden: true,
			},
			type: {
				width: 5,
				sortable: true,
				editor: {
					xtype: 'msoptionsprice-combo-modification-type',
					fieldLabel: _('msoptionsprice_type'),
					name: 'type',
					allowBlank: false
				},
				renderer: function (value, metaData, record) {
					return MODx.lang['msoptionsprice_modification_name_type_' + value] || value;
				}
			},
			price: {
				width: 10,
				sortable: true,
				editor: {
					xtype: 'textfield',
					allowBlank: false
				}
			},
			article: {
				width: 10,
				sortable: true,
				editor: {
					xtype: 'textfield',
					allowBlank: true
				}
			},
			weight: {
				width: 10,
				sortable: true,
				editor: {
					xtype: 'numberfield',
					decimalPrecision: 3,
					allowBlank: true
				}
			},
			count: {
				width: 10,
				sortable: true,
				editor: {
					xtype: 'numberfield',
					decimalPrecision: 0,
					allowBlank: true
				}
			},
			image: {
				width: 15,
				sortable: true,
				editor: {
					xtype: 'msoptionsprice-combo-product-image',
					fieldLabel: _('msoptionsprice_image'),
					name: 'image',
					rid: config.resource.id || 0,
					custm: true,
					clear: true,
					allowBlank: true
				},
				renderer: function (value, metaData, record) {
					return msoptionsprice.tools.renderReplace(record['json']['image'], record['json']['image_name'])
				}
			},
			actions: {
				width: 10,
				sortable: false,
				id: 'actions',
				renderer: msoptionsprice.tools.renderActions,

			}
		};

		var fields = this.getFields();
		fields.filter(function (field) {
			if (add[field]) {
				Ext.applyIf(add[field], {
					header: _('msoptionsprice_header_' + field),
					tooltip: _('msoptionsprice_tooltip_' + field),
					dataIndex: field
				});
				columns.push(add[field]);
			}
		});

		return columns;
	},

	getListeners: function (config) {
		return Ext.applyIf(config.listeners, {
			render: {
				fn: this.dd,
				scope: this
			}
		});
	},

	getMenu: function (grid, rowIndex) {
		var ids = this._getSelectedIds();
		var row = grid.getStore().getAt(rowIndex);
		var menu = msoptionsprice.tools.getMenu(row.data['actions'], this, ids);
		this.addContextMenuItem(menu);
	},

	onClick: function (e) {
		var elem = e.getTarget();
		if (elem.nodeName == 'BUTTON') {
			var row = this.getSelectionModel().getSelected();
			if (typeof(row) != 'undefined') {
				var action = elem.getAttribute('action');
				if (action == 'showMenu') {
					var ri = this.getStore().find('id', row.id);
					return this._showMenu(this, ri, e);
				} else if (typeof this[action] === 'function') {
					this.menu.record = row.data;
					return this[action](this, e);
				}
			}
		}
		return this.processEvent('click', e);
	},

	setAction: function (method, field, value) {
		var ids = this._getSelectedIds();
		if (!ids.length && (field !== 'false')) {
			return false;
		}
		MODx.Ajax.request({
			url: msoptionsprice.config.connector_url,
			params: {
				action: 'mgr/modification/multiple',
				method: method,
				field_name: field,
				field_value: value,
				ids: Ext.util.JSON.encode(ids)
			},
			listeners: {
				success: {
					fn: function () {
						this.refresh();
					},
					scope: this
				},
				failure: {
					fn: function (response) {
						MODx.msg.alert(_('error'), response.message);
					},
					scope: this
				}
			}
		})
	},

	remove: function () {
		Ext.MessageBox.confirm(
			_('msoptionsprice_action_remove'),
			_('msoptionsprice_confirm_remove'),
			function (val) {
				if (val == 'yes') {
					this.setAction('remove');
				}
			},
			this
		);
	},


	active: function (btn, e) {
		this.setAction('setproperty', 'active', 1);
	},

	inactive: function (btn, e) {
		this.setAction('setproperty', 'active', 0);
	},

	create: function (btn, e) {
		var record = {
			id: 0,
			rid: this.config.resource.id,
			type: 1,
			price: 0,
			active: 1,
		};

		var w = MODx.load({
			xtype: 'msoptionsprice-window-modification',
			action: 'mgr/modification/create',
			record: record,
			listeners: {
				success: {
					fn: function () {
						this.refresh();
					}, scope: this
				}
			}
		});
		w.reset();
		w.setValues(record);
		w.show(e.target);
	},

	update: function (btn, e, row) {
		if (typeof(row) != 'undefined') {
			this.menu.record = row.data;
		}
		else if (!this.menu.record) {
			return false;
		}
		var id = this.menu.record.id;
		MODx.Ajax.request({
			url: this.config.url,
			params: {
				action: 'mgr/modification/get',
				id: id
			},
			listeners: {
				success: {
					fn: function (r) {
						var record = r.object;
						var w = MODx.load({
							xtype: 'msoptionsprice-window-modification',
							title: _('msoptionsprice_action_update'),
							action: 'mgr/modification/update',
							record: record,
							update: true,
							listeners: {
								success: {
									fn: function () {
										this.refresh();
									}, scope: this
								}
							}
						});
						w.reset();
						w.setValues(record);
						w.show(e.target);
					}, scope: this
				}
			}
		});
	},

	_filterByCombo: function (cb) {
		this.getStore().baseParams[cb.name] = cb.value;
		this.getBottomToolbar().changePage(1);
	},

	_doSearch: function (tf) {
		this.getStore().baseParams.query = tf.getValue();
		this.getBottomToolbar().changePage(1);
	},

	_clearSearch: function () {
		this.getStore().baseParams.query = '';
		this.getBottomToolbar().changePage(1);
	},

	_updateRow: function () {
		this.refresh();
	},

	_getSelectedIds: function () {
		var ids = [];
		var selected = this.getSelectionModel().getSelections();

		for (var i in selected) {
			if (!selected.hasOwnProperty(i)) {
				continue;
			}
			ids.push(selected[i]['id']);
		}

		return ids;
	}

});
Ext.reg('msoptionsprice-grid-modification', msoptionsprice.grid.modification);