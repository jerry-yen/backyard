(function($) {

    /**
     * 組件下拉選單元件
     * 
     * @param {*} _settings 設定值
     */
    $.widgets_component = function(_settings) {
        var settings = $.extend({
            'id': '',
            'tip': '',
            'name': '',
            'value': '',
            'class': 'form-control',
            'label': '',
            'source': '',
            'component': $('\
                <div>\
                    <button type="button" class="add_widget btn bg-green float-right">\
                        <i class="fas fa-plus"></i> 新增\
                    </button>\
                    <div class="clearfix"></div>\
                    <div class="widgets">\
                    </div>\
                </div>\
            '),
            'emptyItem': $('\
                <div class="widget-block">\
                    <div class="card-header">\
                        組件資訊\
                        <button type="button" class="remove btn bg-red float-right"><i class="fas fa-trash-alt"></i></button>\
                    </div>\
                    <div class="form-group">\
                        <label>組件</label>\
                        <select name="widget"></select>\
                    </div>\
                    <div class="form-group">\
                        <table style="width:100%;">\
                            <tr>\
                                <td colspan="3">桌面 = <span class="desktop_value value">12</span></td>\
                            </tr>\
                            <tr>\
                                <td style="width:10%;text-align: right;padding: 0px 15px;font-size: 15px;font-weight: bold;">1</td>\
                                <td><div class="slider desktop" style="70%"></div></td>\
                                <td style="width:10%;text-align: left;padding: 0px 15px;font-size: 15px;font-weight: bold;">12</td>\
                            </tr>\
                        </table>\
                    </div>\
                    <div class="form-group">\
                        <table style="width:100%;">\
                            <tr>\
                                <td colspan="3">平板 = <span class="pad_value value">12</span></td>\
                            </tr>\
                            <tr>\
                                <td style="width:10%;text-align: right;padding: 0px 15px;font-size: 15px;font-weight: bold;">1</td>\
                                <td><div class="slider pad" style="70%"></div></td>\
                                <td style="width:10%;text-align: left;padding: 0px 15px;font-size: 15px;font-weight: bold;">12</td>\
                            </tr>\
                        </table>\
                    </div>\
                    <div class="form-group">\
                        <table style="width:100%;">\
                            <tr>\
                                <td colspan="3">手機 = <span class="mobile_value value">12</span></td>\
                            </tr>\
                            <tr>\
                                <td style="width:10%;text-align: right;padding: 0px 15px;font-size: 15px;font-weight: bold;">1</td>\
                                <td><div class="slider mobile" style="70%"></div></td>\
                                <td style="width:10%;text-align: left;padding: 0px 15px;font-size: 15px;font-weight: bold;">12</td>\
                            </tr>\
                        </table>\
                    </div>\
                </div>\
            ')
        }, _settings);

        var components = [];

        // 自定義函式
        var coreMethod = {
            initial: function() {
                settings.component
                    .attr('id', settings.id)
                    .attr('name', settings.name);

                $('select', settings.emptyItem)
                    .attr('class', settings.class)


                $backyard.utility.api(
                    'api/widgets', {},
                    'GET',
                    function(response) {
                        $('select', settings.emptyItem).append('<option value="">請選擇</option>');
                        for (var key in response.items) {
                            $('select', settings.emptyItem).append('<option value="' + response.items[key].code + '">' + response.items[key].name + '</option>');
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
            elementConvertToComponent: function() {
                $('body').on('click', '#' + settings.id + ' button.add_widget', function() {
                    var widget = settings.emptyItem.clone();
                    var slider = $('div.slider', widget)
                    slider.slider({
                        'range': 'min',
                        'min': 1,
                        'max': 12,
                        slide: function(event, ui) {
                            var container = $(this).closest('table');
                            $('span.value', container).html(ui.value);
                        }
                    });
                    slider.slider('value', 12);

                    $('button.remove', widget).click(function() {
                        $(this).closest('div.widget-block').remove();
                    });

                    $('div.widgets', settings.component).append(widget);
                });

                $('div.widgets', settings.component).sortable({
                    handle: "div.card-header"
                });

            },
            getName: function() {
                return settings.name;
            },
            getValue: function() {
                var widgets = [];
                $('div.widget-block', settings.component).each(function() {
                    var widget = $('select[name="widget"]', $(this)).val();
                    var desktop = $('span.desktop_value', $(this)).html();
                    var pad = $('span.pad_value', $(this)).html();
                    var mobile = $('span.mobile_value', $(this)).html();
                    widgets.push({ 'code': widget, 'desktop': desktop, 'pad': pad, 'mobile': mobile });
                });

                if (widgets.length == 0) {
                    return '';
                }
                return widgets;
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

                $('div.widgets *', settings.component).remove();

                if (value == undefined || value == '') {
                    value = [];
                }
                for (var key in value) {
                    var widget = settings.emptyItem.clone();

                    $('select[name="widget"]', widget).val(value[key].code);
                    $('div.slider', widget).slider({
                        'range': 'min',
                        'min': 1,
                        'max': 12,
                        slide: function(event, ui) {
                            var container = $(this).closest('table');
                            $('span.value', container).html(ui.value);
                        }
                    });


                    $('div.slider.desktop', widget).slider('value', value[key].desktop);
                    $('div.slider.pad', widget).slider('value', value[key].pad);
                    $('div.slider.mobile', widget).slider('value', value[key].mobile);

                    $('span.desktop_value', widget).html(value[key].desktop);
                    $('span.pad_value', widget).html(value[key].pad);
                    $('span.mobile_value', widget).html(value[key].mobile);

                    $('button.remove', widget).click(function() {
                        $(this).closest('div.widget-block').remove();
                    });

                    $('div.widgets', settings.component).append(widget);
                }
            },
            showInList: function(value) {
                return value;
            }
        };

        return coreMethod;
    };
}(jQuery));