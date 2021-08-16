(function($) {

    /**
     * 下拉選單元件
     * 
     * @param {*} _settings 設定值
     */
    $.select_component = function(_settings) {
        var settings = $.extend({
            'id': '',
            'tip': '',
            'name': '',
            'value': '',
            'class': 'form-control',
            'label': '',
            'source': '',
            'component': $('<select></select>')
        }, _settings);

        // 自定義函式
        var coreMethod = {
            initial: function() {
                settings.component
                    .attr('id', settings.id)
                    .attr('class', settings.class)
                    .attr('name', settings.name)
                    .val(settings.value);
                if (settings.source.indexOf('api://') === 0) {
                    var apiUrl = settings.source.substring(6);
                    $.backyard().process.api('/index.php/api/' + apiUrl, {}, 'GET', function(response) {
                        for (var key in response) {
                            settings.component.append('<option value="' + key + '">' + response[key] + '</option>');
                        }
                    });
                } else {
                    var source = (settings.source == '' || settings.source == undefined) ? [] : JSON.parse(settings.source);
                    for (var key in source) {
                        settings.component.append('<option value="' + key + '">' + source[key] + '</option>');
                    }
                }

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
            elementConvertToComponent: function() {},
            getName: function() {
                return settings.name;
            },
            getValue: function() {
                return settings.component.val();
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
                settings.component.val(value);
            },
            showInList: function(value) {
                return value;
            }
        };

        return coreMethod;
    };
}(jQuery));