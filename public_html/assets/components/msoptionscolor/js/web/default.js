msoptionscolor = {
    initialize: function() {
        if(!jQuery().select2) {
            document.write('<script src="'+msoptionscolorConfig.jsUrl+'select2/select2.min.js"><\/script>');
        }
        $(document).ready(function() {
            $('select.msoptionscolor').select2({
                templateResult: msoptionscolor.template.Result
                ,templateSelection: msoptionscolor.template.Selection
                ,language: {
                    noResults: function () {
                        return "Совпадений нет...";
                    }
                }
            });
        });
    }
};

msoptionscolor.template = {
    Result: function(el) {
        if (!el.id) { return el.text; }
        var color = el.element.dataset.color;
        var pattern = el.element.dataset.pattern;
        if (!!pattern) {
            var $color = $('<span><img src="/' + pattern + '" class="msoptionscolor-pattern" /> ' + el.text + '</span>');
        }
        else {
            var $color = $('<span><div class="msoptionscolor-color" style="background:#' + color + '"></div><div class="msoptionscolor-text">' + el.text + '</div></span>');
        }
        return $color;
    }

    ,Selection: function(el) {
        if (!el.id) { return el.text; }
        var color = el.element.dataset.color;
        var pattern = el.element.dataset.pattern;
        if (!!pattern) {
            var $color = $('<span><img src="/' + pattern + '" class="msoptionscolor-pattern" /> ' + el.text + '</span>');
        }
        else {
            var $color = $('<span><div class="msoptionscolor-color" style="background:#' + color + '"></div><div class="msoptionscolor-text">' + el.text + '</div></span>');
        }
        return $color;
    }
};

msoptionscolor.initialize();