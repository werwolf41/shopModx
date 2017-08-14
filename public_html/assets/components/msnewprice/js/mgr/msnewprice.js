var msnewprice = function (config) {
	config = config || {};
	msnewprice.superclass.constructor.call(this, config);
};
Ext.extend(msnewprice, Ext.Component, {
	page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, view: {}, utils: {}
});
Ext.reg('msnewprice', msnewprice);

msnewprice = new msnewprice();