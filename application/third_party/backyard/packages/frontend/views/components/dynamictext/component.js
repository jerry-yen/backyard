(function($) {

    /**
     * 動態新增文字方塊元件
     * 
     * @param {*} _settings 設定值
     */
    $.dynamictext_component = function(_settings) {
        var settings = $.extend({
            'id': '',
            'tip': '',
            'name': '',
            'value': '',
            'class': 'form-control',
            'label': '',
            'source': '',
            'component': $('<div>\
                                <button type="button" class="add-item btn bg-green float-right"><i class="fas fa-plus"></i></button>\
                                <div class="clearfix"></div>\
                                <br />\
                                <div class="wrap"></div>\
                            </div>'),
            'emptyItem': $('<div class="item" style="margin-bottom:3px;"><input type="text" class="form-control float-left" style="width:90%;"/><button type="button" class="delete-item btn bg-red float-right"><i class="fas fa-trash"></i></button><div class="clearfix"></div></div>')
        }, _settings);

        var source = (settings.source == '' || settings.source == undefined) ? [] : JSON.parse(settings.source);

        // 自定義函式
        var coreMethod = {
            initial: function() {
                settings.component
                    .attr('id', settings.id)
                    .attr('name', settings.name);
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
                if ($('div.wrap div.item', settings.component).length == 0) {
                    var item = settings.emptyItem.clone();
                    $('div.wrap', settings.component).append(item);
                }

                // 新增
                $(settings.component).on('click', 'button.add-item', function() {
                    var item = settings.emptyItem.clone();
                    $('div.wrap', settings.component).append(item);
                });

                // 刪除
                $(settings.component).on('click', 'button.delete-item', function() {
                    $(this).closest('div.item').remove();
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
                return value;
            }
        };

        return coreMethod;
    };
}(jQuery));