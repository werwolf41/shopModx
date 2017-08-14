Msnewprice = {
    add: {
        options: {
            add: '.msnewprice-add',
            remove: '.msnewprice-remove',
            added: 'added',
            loading: 'loading'
        },
        initialize: function(selector, params) {
            if (!$(selector).length) {return;}
            var options = this.options;
            $(document).on('click', selector + ' ' + options.add + ',' + selector + ' ' + options.remove, function() {
                var $this = $(this);
                var $parent = $this.parents(selector);
                var id = $parent.data('id');
                var list = $parent.data('list');
                var text = $this.data('text');
                var action = $this.hasClass(options.add.substr(1))
                    ? 'add'
                    : 'remove';
                if ($this.hasClass(options.loading)) {return false;}
                else {$this.addClass(options.loading);}
                if (text.length) {
                    $this.attr('data-text', $this.text()).text(text);
                }
                $.post(document.location.href, {msnewprice_action: action, resource: id, list: list}, function(response) {
                    if (text.length) {
                        text = $this.attr('data-text');
                        $this.attr('data-text', $this.text()).text(text);
                    }
                    $this.removeClass(options.loading);
                    if (response.success) {
                        $(options.total, selector).text(response.data.total);
                        if (action == 'add') {$parent.addClass(options.added);}
                        else {$parent.removeClass(options.added);}
                        if((typeof miniShop2 != 'undefined') && (response.message != 'undefined')){
                            miniShop2.Message.success(response.message);
                        }
                        else {alert(response.message);}
                    }
                    else {
                        if (typeof miniShop2 != 'undefined') {
                            miniShop2.Message.error(response.message);
                        }
                        else {alert(response.message);}
                    }
                }, 'json');
                return false;
            });
        }
    },

};