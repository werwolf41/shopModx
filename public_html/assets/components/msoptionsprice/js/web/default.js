/* 2.1.6 */
(function (window, document, $, msOptionsPriceConfig) {

	var msOptionsPrice = msOptionsPrice || {};


	msOptionsPrice.setup = function () {
		msOptionsPrice.$doc = $(document);

		msOptionsPrice.Product.form = '.ms2_form.msoptionsprice-product';
		msOptionsPrice.Product.cost = '.msoptionsprice-cost';
		msOptionsPrice.Product.mass = '.msoptionsprice-mass';
		msOptionsPrice.Product.article = '.msoptionsprice-article';
		msOptionsPrice.Product.weight = '.msoptionsprice-weight';
		msOptionsPrice.Product.count = '.msoptionsprice-count';

		msOptionsPrice.Product.gallery = '.msoptionsprice-gallery';
		msOptionsPrice.Product.fotorama = '.fotorama';
		msOptionsPrice.Product.image = '.msoptionsprice-image';
		msOptionsPrice.Product.prefix = '.msoptionsprice-';
	};


	msOptionsPrice.initialize = function () {
		msOptionsPrice.setup();

		/* get modification on change options */
		msOptionsPrice.$doc.on('change', msOptionsPrice.Product.form, function (e) {
			msOptionsPrice.Product.action('modification/get', this);
			e.preventDefault();
			return false;
		});

		/* get modification on document ready */
		msOptionsPrice.$doc.ready(function () {
			msOptionsPrice.mSearch2.setOptionsByFilter();
			$(msOptionsPrice.Product.form).each(function () {
				msOptionsPrice.Product.action('modification/get', this);
			});
		});

		/* get modification on change image */
		$(msOptionsPrice.Product.gallery + ' ' + msOptionsPrice.Product.fotorama)
			.on('fotorama:show', function (e, fotorama, extra) {

				var item = fotorama.activeFrame;
				if (extra.user && item.rid && item.iid) {

					var form = $(msOptionsPrice.Product.cost + msOptionsPrice.Product.prefix + item.rid)
						.closest(msOptionsPrice.Product.form);
					if (form) {
						msOptionsPrice.Product.action('modification/get', form, {id: item.rid, iid: item.iid});
					}

				}
			});

		/* set rid, iid image */
		$(msOptionsPrice.Product.gallery + ' ' + msOptionsPrice.Product.fotorama)
			.on('fotorama:load', function (e, fotorama, extra) {

				fotorama.data.filter(function (item, r) {
					if (item.rid && item.iid && item.thumb) {
						var thumb = $(this).find('img[src$="' + item.thumb + '"]');

						if (thumb) {
							thumb
								.data('i', item.i)
								.attr('data-i', item.i)
								.data('rid', item.rid)
								.attr('data-rid', item.rid)
								.data('iid', item.rid)
								.attr('data-iid', item.iid);
						}

						if (fotorama.activeFrame.i == item.i) {
							var form = $(msOptionsPrice.Product.cost + msOptionsPrice.Product.prefix + item.rid)
								.closest(msOptionsPrice.Product.form);
							if (form) {
								msOptionsPrice.Product.action('modification/get', form, {id: item.rid, iid: item.iid});
							}
						}
					}

				}, this);
			});

		/* set options and get modification on "mse2_load" */
		msOptionsPrice.$doc.on('mse2_load', function (e, response) {
			msOptionsPrice.mSearch2.setOptionsByFilter();
			$(msOptionsPrice.Product.form).each(function () {
				msOptionsPrice.Product.action('modification/get', this);
			});
		});

	};


	msOptionsPrice.mSearch2 = {

		setOptionsByFilter: function () {
			/* if mse2Config set options */
			if (typeof(mse2Config) != 'undefined' && mSearch2.initialized) {
				var filterDelimeter = mse2Config.filter_delimeter || '|';
				var valuesDelimeter = mse2Config.values_delimeter || ',';
				var filters = mSearch2.getFilters();

				for (i in filters) {
					if (!filters.hasOwnProperty(i)) {
						continue;
					}
					var filterValue = filters[i].split(valuesDelimeter);
					var filterName = i.split(filterDelimeter);

					switch (filterName[0]) {

						case 'msoption':
							msOptionsPrice.Tools.setInputValue(filterName[1], filterValue);
							break;

					}
				}
			}
		},

	};


	msOptionsPrice.Product = {

		action: function (action, form, data) {

			var formData = new FormData($(form)[0]);

			if (!msOptionsPrice.Tools.empty(data)) {
				for (key in data) {
					if (!data.hasOwnProperty(key)) {
						continue;
					}
					formData.append(key, data[key]);
				}
			}

			formData.append('action', action);
			formData.append('ctx', msOptionsPriceConfig.ctx);

			$.ajax({
				type: 'POST',
				url: msOptionsPriceConfig.actionUrl,
				dataType: 'json',
				data: formData,
				async: true,
				cache: false,
				contentType: false,
				processData: false,
				beforeSend: function () {
					return true;
				},
				success: function (response) {

					msOptionsPrice.$doc.trigger('msoptionsprice_product_action', [action, form, response]);

					if (response.success && response.data) {

						var data = response.data;
						var errors = [];
						var fotorama = $(msOptionsPrice.Product.fotorama).data('fotorama');

						if (!msOptionsPrice.Tools.empty(data.errors)) {
							errors.push(data.errors);
						}

						if (!msOptionsPrice.Tools.empty(data.modification)) {
							['article', 'count', 'weight', 'cost', 'mass'].filter(function (key) {
								if (msOptionsPrice.Product[key]) {

									var value = data.modification[key];
									value = msOptionsPrice.Tools.formatOptionValue(key, value);

									$(msOptionsPrice.Product[key] + msOptionsPrice.Product.prefix + data.rid).html(value);
								}
							});

						}

						/* set image */
						if (!msOptionsPrice.Tools.empty(data.modification.image) && msOptionsPrice.Tools.empty(data.set.options)) {

							if (miniShop2.Gallery && miniShop2.Gallery.files) {
								var fotorama = miniShop2.Gallery.files.data('fotorama');

								fotorama.data.filter(function (item, r) {
									if (item['rid'] == data.rid && item['iid'] == data.modification.image) {
										fotorama.show(item['i'] - 1);
									}
								}, this);
							}
						}

						/* set options */
						if (!msOptionsPrice.Tools.empty(data.set.options)) {

							for (key in data.options) {
								if (!data.options.hasOwnProperty(key)) {
									continue;
								}

								var value = data.options[key];
								value = msOptionsPrice.Tools.formatOptionValue(key, value);

								msOptionsPrice.Tools.setInputValue(key, value, data.rid);
							}
						}


						if (!msOptionsPrice.Tools.empty(errors)) {
							console.log(errors.join('<br>'));
						}
					}
					else if (!response.success) {

					}
				}
			}).done(function (response) {

			}).fail(function (jqXHR, textStatus, errorThrown) {

			});
		}

	};


	msOptionsPrice.Tools = {

		arrayIntersect: function (array1, array2) {
			var result = array1.filter(function (n) {
				return array2.indexOf(n) !== -1;
			});

			return result;
		},

		inArray: function (needle, haystack) {
			for (key in haystack) {
				if (haystack[key] == needle) return true;
			}

			return false;
		},

		empty: function (value) {
			return (typeof(value) == 'undefined' || value == 0 || value === null || value === false || (typeof(value) == 'string' && value.replace(/\s+/g, '') == '') || (typeof(value) == 'object' && value.length == 0));
		},

		setInputValue: function (key, value, rid) {

			var inputs;

			if (rid) {
				inputs = $(msOptionsPrice.Product.cost + msOptionsPrice.Product.prefix + rid)
					.closest(msOptionsPrice.Product.form)
					.find('[name="options[' + key + ']"]');
			}
			else {
				inputs = $(msOptionsPrice.Product.cost)
					.closest(msOptionsPrice.Product.form)
					.find('[name="options[' + key + ']"]');
			}

			if (!inputs) {
				return false;
			}

			inputs.each(function () {

				var $this = $(this);
				switch ($this[0].tagName) {

					case 'INPUT':
						$this.val(value);
						break;
					case 'SELECT':
						if (!(value instanceof Array)) {
							value = [value];
						}
						value.filter(function (item, r) {
							if ($this.find('option[value="' + item + '"]').length) {
								$this.val([item]);
							}
						}, this);
						break;
				}
			});

		},

		formatOptionValue: function (key, value) {

			switch (key) {
				case 'cost':
				case 'price':
					if (miniShop2 && miniShop2.Utils.formatPrice) {
						value = miniShop2.Utils.formatPrice(value);
					}
					break;
				case 'mass':
				case 'weight':
					if (miniShop2 && miniShop2.Utils.formatWeight) {
						value = miniShop2.Utils.formatWeight(value);
					}
					break;
				default:
					break;
			}

			return value;
		}

	};


	$(document).ready(function ($) {

	});


	msOptionsPrice.initialize();
	window.msOptionsPrice = msOptionsPrice;

})(window, document, jQuery, msOptionsPriceConfig);


/* event example */
$(document).on('msoptionsprice_product_action', function (e, action, form, response) {

	//console.log(action, form, response);
});
