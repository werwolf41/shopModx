msnewpriceComboChunk = function(config) {
    config = config || {};
    Ext.applyIf(config, {
        name: 'chunk'
        , hiddenName: 'chunk'
        , displayField: 'name'
        , valueField: 'id'
        , editable: true
        , fields: ['id', 'name']
        , pageSize: 20
        , emptyText: _('msnewprice_combo_select')
        , hideMode: 'offsets'
        , url: MODx.config.connector_url
        , baseParams: {
            action: 'element/chunk/getlist'
        }
    });
    msnewpriceComboChunk.superclass.constructor.call(this, config);
};
Ext.extend(msnewpriceComboChunk, MODx.combo.ComboBox);
Ext.reg('msnewprice-combo-chunk', msnewpriceComboChunk);