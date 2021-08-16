(function($) {

    /**
     * 表單組件
     * 
     * @param {*} _settings 設定值
     */
    $.fn.widget_form = function(_settings) {
        var settings = $.extend({
            'userType': 'admin',
            'code': $(this).attr('widget'),
            'instance': this,
            'submit_button_selector': 'button.submit'
        }, _settings);

        var metadata = [];
        var components = [];

        // 自定義函式
        var widget = {
            form: {
                initial: function() {

                    metadata = $backyard.template.widgets[settings.code];

                    // 組件標題
                    $('h3.card-title', settings.instance).html(metadata.name);

                    var fields = metadata.dataset.fields;

                    // 呈現欄位元件
                    for (var key in fields) {
                        var componentName = fields[key].component + '_component';
                        var component = new $[componentName]({
                            'id': fields[key].frontendVariable,
                            'name': fields[key].frontendVariable,
                            'tip': fields[key].fieldTip,
                            'source': fields[key].source,
                            'label': fields[key].name,
                            'userType': settings.userType,
                            'code': settings.code
                        });
                        component.initial();

                        var fieldContainer = $('<div class="form-group"></div>');
                        fieldContainer.append(component.label());
                        fieldContainer.append(component.tip());
                        fieldContainer.append(component.invalid());
                        fieldContainer.append('<br />');
                        fieldContainer.append(component.element());
                        component.elementConvertToComponent();

                        $('div.card-body', settings.instance).append(fieldContainer);

                        components[fields[key].frontendVariable] = component;
                    }

                    widget.form.event.submit();
                },
                data: function() {

                    var url = (metadata.setting.data_event == undefined || metadata.setting.data_event == '') ? ('api/item/' + metadata.dataset.code) : metadata.setting.data_event;

                    $backyard.utility.api(
                        url, {},
                        'GET',
                        function(response) {
                            // 將資料代入到各個欄位
                            if (response.status == 'success') {
                                for (var fieldName in response.item) {
                                    if (components[fieldName] != undefined) {
                                        components[fieldName].setValue(response.item[fieldName]);
                                    }
                                }

                                // 如果預設有id，代表為修改模式
                                if (response.item != null && response.item['id'] != undefined && response.item['id'] != null) {
                                    $('div.card-body', settings.instance).append('<input type="hidden" id="id" name="id" value="' + response.item['id'] + '"/>')
                                }
                            }
                        }
                    );
                },
                event: {
                    /**
                     * 送出表單
                     */
                    submit: function() {
                        $(settings.submit_button_selector).click(function() {

                            var data = {};

                            // 取得所有隱藏欄位值，包含id
                            $('input[type="hidden"]').each(function() {
                                data[$(this).attr('name')] = $(this).val();
                            });

                            // 取得各欄位(元件)的值
                            for (var key in components) {
                                data[components[key].getName()] = components[key].getValue();
                                components[key].setInvalid('');
                            }

                            httpType = (data['id'] != undefined) ? 'PUT' : 'POST';
                            var url = (metadata.setting.submit_event == undefined || metadata.setting.submit_event == '') ? ('api/item/' + metadata.dataset.code) : metadata.setting.submit_event;
                            $backyard.utility.api(
                                url,
                                data,
                                httpType,
                                function(response) {
                                    if (response.status == 'failed') {
                                        // 欄位驗證失敗
                                        if (response.code == 'validator') {
                                            for (var fieldName in response.message) {
                                                components[fieldName].setInvalid(response.message[fieldName]);
                                            }
                                        }
                                    } else {
                                        if ($('div.card-body input[id="id"]', settings.instance).length == 0 && response.id != undefined) {
                                            $('div.card-body', settings.instance).append('<input type="hidden" id="id" name="id" value="' + response.id + '"/>')
                                        }

                                        $backyard.dialog.alert('儲存成功', '', 'success');
                                    }
                                }
                            );

                        });
                    }
                },
            }
        }

        widget.form.initial();
        widget.form.data();

        return widget;
    };
}(jQuery));