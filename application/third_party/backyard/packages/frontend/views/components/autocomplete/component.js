(function($) {

    /**
     * 自動完成輸入框元件
     * 
     * @param {*} _settings 設定值
     */
    $.autocomplete_component = function(_settings) {
        var settings = $.extend({
            'id': '',
            'tip': '',
            'name': '',
            'value': '',
            'class': 'form-control',
            'label': '',
            'source': '',
            'component': $('<div><input type="text"><input type="hidden"></div>')
        }, _settings);

        var source = (settings.source == '' || settings.source == undefined) ? [] : JSON.parse(settings.source);

        // 自定義函式
        var coreMethod = {
            initial: function() {
                $('input[type="text"]', settings.component)
                    .attr('id', settings.id + '_text')
                    .attr('class', settings.class)
                    .attr('name', settings.name + '_text')
                    .val(settings.value);
                $('input[type="hidden"]', settings.component)
                    .attr('id', settings.id)
                    .attr('name', settings.name)
                    .val(settings.value);
            },
            tip: function() {
                return $('<tip for="' + settings.id + '">' + settings.tip + '</tip>');
            },
            label: function() {
                return $('<label for="' + settings.id + '">' + settings.label + ' : </label>');
            },
            invalid: function() {
                return $('<invalid for="' + settings.id + '" style="display:none;"></invalid>');
            },
            element: function() {
                return settings.component;
            },
            elementConvertToComponent: function() {
                $('input[name="' + settings.name + '_text"]', settings.component).autocomplete({
                    'source': '/' + source.api,
                    'minLength': source.minlength,
                    select: function(event, ui) {
                        $('input[type="hidden"]', settings.component).val(ui.item.value);
                    }
                });
            },
            getName: function() {
                return settings.name;
            },
            getValue: function() {
                return $('input[type="hidden"]', settings.component).val();
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
                $('input[type="hidden"]', settings.component).val(value);

                $.get('/' + source.reverse_api, { 'value': value }, function(response) {
                    $('input[type="text"]', settings.component).val(response.text);
                }, 'JSON');
            },
            showInList: function(value) {
                $.get('/' + source.reverse_api, { 'value': value }, function(response) {
                    $('i.ac_' + settings.id).prop("outerHTML", response.text);
                }, 'JSON');

                return '<i class="fas fa-spinner fa-spin ac_' + settings.id + '"></i>';
            }
        };

        return coreMethod;
    };
}(jQuery));