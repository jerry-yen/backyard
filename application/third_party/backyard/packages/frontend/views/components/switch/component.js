(function($) {

    /**
     * 開關元件
     * 
     * @param {*} _settings 設定值
     */
    $.switch_component = function(_settings) {
        var settings = $.extend({
            'id': '',
            'tip': '',
            'name': '',
            'value': '',
            'class': 'form-control',
            'label': '',
            'source': '',
            'component': $('<input type="checkbox" value="Y" data-on-color="primary" data-off-color="danger">')
        }, _settings);

        // 自定義函式
        var coreMethod = {
            initial: function() {
                settings.component
                    .attr('id', settings.id)
                    //                    .attr('class', settings.class)
                    .attr('name', settings.name);
                //                    .val(settings.value);
                var source = JSON.parse(settings.source);
                settings.component.attr('data-on-text', source[0]);
                settings.component.attr('data-off-text', source[1]);

            },
            tip: function() {
                return $('<tip for="' + settings.id + '">' + settings.tip + '</tip>');
            },
            label: function() {
                return $('<label for="' + settings.id + '">' + settings.label + ' : </label>');
            },
            element: function() {
                return settings.component;
            },
            invalid: function() {
                return $('<invalid for="' + settings.id + '" style="display:none;"></invalid>');
            },
            elementConvertToComponent: function() {
                settings.component.bootstrapSwitch();
            },
            getName: function() {
                return settings.name;
            },
            getValue: function() {
                return settings.component.bootstrapSwitch('state') ? 'Y' : 'N';
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
                // 剛載入時需要延遲，才有辦法設定value，否則會被預設值蓋過
                setTimeout(function() {
                    settings.component.bootstrapSwitch('state', (value == 'Y'));
                }, 500);
            },
            showInList: function(value) {
                return value;
            }
        };

        return coreMethod;
    };
}(jQuery));