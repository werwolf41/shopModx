msoptionscolor.utils.renderBoolean = function (value, props, row) {

	return value
		? String.format('<span class="green">{0}</span>', _('yes'))
		: String.format('<span class="red">{0}</span>', _('no'));
};

msoptionscolor.utils.renderColor = function (value, props, row) {

	return value
		? String.format('<div style="width: 45px;  border-radius: 3px; padding: 5px;margin: -5px; border: 1px solid #E4E4E4;background: #' + value + '"><span>&nbsp;' + value + '</span></div>')
		: String.format('<span class="red">{0}</span>', _('no'));
};

msoptionscolor.utils.renderPattern = function (value, props, row) {

	return value
		? String.format('<div style="width: 45px;  border-radius: 3px; padding: 5px;margin: -5px;height: 15px;border: 1px solid #E4E4E4;background-image: url(/' + value + ')"></div>')
		: String.format('<span class="red">{0}</span>', _('no'));
};
