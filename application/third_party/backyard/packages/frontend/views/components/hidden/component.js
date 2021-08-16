(function($) {

    /**
     * 隱藏元件
     * 
     * @param {*} _settings 設定值
     */
    $.hidden_component = function(_settings) {
        var settings = $.extend({
            'id': '',
            'tip': '',
            'name': '',
            'value': '',
            'class': 'form-control',
            'label': '',
            'source': '',
            'component': $('<input type="hidden">')
        }, _settings);

        // 自定義函式
        var coreMethod = {
            initial: function() {
                settings.component
                    .attr('id', settings.id)
                    .attr('class', settings.class)
                    .attr('name', settings.name)
                    .val(settings.value);
            },
            tip: function() {
                return '';
            },
            label: function() {
                return '';
            },
            invalid: function() {
                return '';
            },
            element: function() {
                return settings.component;
            },
            elementConvertToComponent: function() {
                settings.component.closest('div.form-group').hide();
            },
            getName: function() {
                return settings.name;
            },
            getValue: function() {
                return settings.component.html();
            },
            setInvalid: function(message) {
                var invalid = $('invalid[for="' + settings.id + '"]');
                if (message.trim() != '') {
                    invalid.html(message);
                    invalid.show();
                } else {
                    invalid.html('');
                    invalid.hide();
                }
            },
            setValue: function(value) {
                settings.component.html(value);
            },
            showInList: function(value) {
                return value;
            }
        };

        return coreMethod;
    };
}(jQuery));