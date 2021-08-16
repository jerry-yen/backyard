(function($) {

    /**
     * 資料集欄位元件
     * 
     * @param {*} _settings 設定值
     */
    $.datasetlist_component = function(_settings) {
        var settings = $.extend({
            'id': '',
            'tip': '',
            'name': '',
            'value': '',
            'class': 'table table-hover text-nowrap',
            'label': '',
            'source': '',
            'component': $('\
                <div>\
                    <select name="source" class="form-control"></select>\
                    <br /><br />\
                    <table>\
                        <thead>\
                            <tr>\
                                <th>名稱</th>\
                                <th>前端變數</th>\
                                <th>後端變數</th>\
                            </tr>\
                        </thead>\
                        <tbody>\
                        </tbody>\
                    </table>\
                </div>'),
            'emptyItem': '\
                            <tr>\
                                <td class="name"></td>\
                                <td class="frontendVariable"></td>\
                                <td class="dbVariable"></td>\
                            </tr>'
        }, _settings);

        // 自定義函式
        var coreMethod = {
            initial: function() {
                settings.component.attr('id', settings.id);
                $('table', settings.component)
                    .attr('class', settings.class)
                    .attr('name', settings.name);

                $backyard.utility.api(
                    'api/datasets', {},
                    'GET',
                    function(response) {
                        console.log(response);
                        var select = $('select[name="source"]', settings.component);
                        select.append('<option value="">請選擇</option>');
                        for (var key in response.items) {
                            select.append('<option value="' + response.items[key].code + '" fields=\'' + response.items[key].fields + '\'>' + response.items[key].name + '</option>');
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
            element: function() {
                return settings.component;
            },
            invalid: function() {
                return $('<invalid for="' + settings.id + '" style="display:none;"></invalid>');
            },
            elementConvertToComponent: function() {

                $('body').on('change', '#' + settings.id + ' select[name="source"]', function() {

                    $('table tbody tr', settings.component).remove();

                    var fieldsValue = $('option:selected', $(this)).attr('fields');
                    if (fieldsValue == undefined) {
                        return;
                    }
                    var fields = JSON.parse($('option:selected', $(this)).attr('fields'));
                    for (var key in fields) {
                        var emptyItem = $(settings.emptyItem);
                        $('td.name', emptyItem).html(fields[key].name);
                        $('td.frontendVariable', emptyItem).html(fields[key].frontendVariable);
                        $('td.dbVariable', emptyItem).html(fields[key].dbVariable);
                        $('table tbody', settings.component).append(emptyItem);
                    }
                });

            },
            getName: function() {
                return settings.name;
            },
            getValue: function() {
                return $('select[name="source"]', settings.component).val();
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
                $('select[name="source"]', settings.component).val(value);
                $('select[name="source"]', settings.component).change();
            },
            showInList: function(value) {
                return value;
            }
        };

        return coreMethod;
    };
}(jQuery));