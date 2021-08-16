(function($) {

    /**
     * 組件下拉選單元件
     * 
     * @param {*} _settings 設定值
     */
    $.pagewidget_component = function(_settings) {
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

        var components = [];

        // 自定義函式
        var coreMethod = {
            initial: function() {
                settings.component
                    .attr('id', settings.id)
                    .attr('class', settings.class)
                    .attr('name', settings.name)
                    .val(settings.value);

                $.backyard({ 'userType': 'master' }).process.api(
                    '/index.php/api/items/user/master/code/widget', {},
                    'GET',
                    function(response) {
                        settings.component.append('<option value="">請選擇</option>');
                        for (var key in response.results) {
                            settings.component.append('<option value="' + response.results[key]._code + '">' + response.results[key].name + '</option>');
                        }
                    }
                );

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