(function($) {

    /**
     * 核取方塊元件
     * 
     * @param {*} _settings 設定值
     */
    $.checkbox_component = function(_settings) {
        var settings = $.extend({
            'id': '',
            'tip': '',
            'name': '',
            'value': '',
            'class': '',
            'label': '',
            'source': '',
            'component': $('<div></div>')
        }, _settings);

        // 自定義函式
        var coreMethod = {
            initial: function() {
                settings.component
                    .attr('id', settings.id)
                    .val(settings.value);

                if (settings.source.indexOf('api://') === 0) {
                    var apiUrl = settings.source.substring(6);
                    $.backyard().process.api('/index.php/api/' + apiUrl, {}, 'GET', function(response) {
                        for (var key in response) {
                            settings.component.append('\
                            <div class="icheck-primary float-left pr-3">\
                                <input id="' + settings.name + '-' + key + '" type="checkbox" name="' + settings.name + '[]" value="' + key + '" />\
                                <label for="' + settings.name + '-' + key + '">' + response[key] + '</label>\
                            </div>\
                            ');
                        }
                    });
                } else {
                    var source = JSON.parse(settings.source);
                    for (var key in source) {
                        settings.component.append('\
                        <div class="icheck-primary float-left pr-3">\
                            <input id="' + settings.name + '-' + key + '" type="checkbox" name="' + settings.name + '[]" value="' + key + '" />\
                            <label for="' + settings.name + '-' + key + '">' + source[key] + '</label>\
                        </div>\
                        ')
                    }
                }
                settings.component.append('<div class="clearfix"></div>');
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
                var values = [];
                $('#' + settings.id + ' input[type="checkbox"]').each(function() {
                    if ($(this).prop('checked')) {
                        values.push($(this).val());
                    }
                });
                return values;
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
                if (value == undefined || value == '') {
                    value = [];
                }
                for (var key in value) {
                    $('input[type="checkbox"][value="' + value[key] + '"', settings.component).prop('checked', true);
                }
            },
            showInList: function(value) {
                return value;
            }
        };

        return coreMethod;
    };
}(jQuery));