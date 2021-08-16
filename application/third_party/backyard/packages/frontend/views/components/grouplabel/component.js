(function($) {

    /**
     * 群組標籤元件
     * 
     * @param {*} _settings 設定值
     */
    $.grouplabel_component = function(_settings) {
        var settings = $.extend({
            'id': '',
            'tip': '',
            'name': '',
            'value': '',
            'class': 'grouplabel',
            'label': '',
            'source': '',
            'component': $('<div></div>')
        }, _settings);

        // 自定義函式
        var coreMethod = {
            initial: function() {
                settings.component
                    .attr('id', settings.id)
                    .attr('class', settings.class)
                    .attr('name', settings.name)
                    .val(settings.value);
                settings.component.html(settings.label);
            },
            tip: function() {
                return '';
            },
            label: function() {
                return;
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

            },
            setValue: function(value) {

            },
            showInList: function(value) {
                return value;
            }
        };

        return coreMethod;
    };
}(jQuery));