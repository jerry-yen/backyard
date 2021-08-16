(function($) {

    /**
     * 輸入框元件
     * 
     * @param {*} _settings 設定值
     */
    $.slider_component = function(_settings) {
        var settings = $.extend({
            'id': '',
            'tip': '',
            'name': '',
            'value': '',
            'class': 'slider form-control',
            'label': '',
            'source': '',
            'component': $('\
                <div class="slider-blue">\
                    <input type="text" data-slider-min="0" data-slider-max="100"\
                    data-slider-step="1" data-slider-value="6" data-slider-orientation="horizontal"\
                    data-slider-selection="before" data-slider-tooltip="show">\
                </div>\
            ')
        }, _settings);

        // 自定義函式
        var coreMethod = {
            initial: function() {
                $('input[type="text"]', settings.component)
                    .attr('id', settings.id)
                    .attr('class', settings.class)
                    .attr('name', settings.name);



                if (settings.source != '') {
                    var attr = JSON.parse(settings.source);
                    if (attr.min != undefined) {
                        $('input[type="text"]', settings.component).attr('data-slider-min', attr.min);
                    }
                    if (attr.max != undefined) {
                        $('input[type="text"]', settings.component).attr('data-slider-max', attr.max);
                    }
                    if (attr.step != undefined) {
                        $('input[type="text"]', settings.component).attr('data-slider-step', attr.step);
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
            elementConvertToComponent: function() {
                $('input[type="text"]', settings.component).bootstrapSlider();
            },
            getName: function() {
                return settings.name;
            },
            getValue: function() {
                return $('input[type="text"]', settings.component).val();
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
                    value = 0;
                }
                $('input[type="text"]', settings.component).bootstrapSlider('setValue', value);
            },
            showInList: function(value) {
                return value;
            }
        };

        return coreMethod;
    };
}(jQuery));