(function($) {

    /**
     * 子項目欄位元件
     * 
     * @param {*} _settings 設定值
     */
    $.sublist_component = function(_settings) {
        var settings = $.extend({
            'id': '',
            'tip': '',
            'name': '',
            'value': '',
            'class': 'table table-hover text-nowrap',
            'label': '',
            'source': '',
            'component': $('\
                <div class="datasetfields">\
                    <button type="button" class="add_field btn bg-green float-right">\
                        <i class="fas fa-plus"></i> 新增\
                    </button>\
                    <br /><br />\
                    <table>\
                        <thead>\
                            <tr>\
                                <th>&nbsp;</th>\
                                <th>ICON</th>\
                                <th>名稱</th>\
                                <th>子項目代碼</th>\
                                <th>子資料集關連欄位</th>\
                                <th>操作</th>\
                            </tr>\
                        </thead>\
                        <tbody>\
                        </tbody>\
                    </table>\
                </div>'),
            'emptyItem': '\
                            <tr>\
                                <td><i class="fas fa-grip-vertical"></i></td>\
                                <td><input type="text" name="icon" class="form-control field_component"></td>\
                                <td><input type="text" name="name" class="form-control field_component"></td>\
                                <td><input type="text" name="widget" class="form-control"></td>\
                                <td><input type="text" name="linkfield" class="form-control"></td>\
                                <td><button type="button" name="delete" class="btn bg-red"><i class="fas fa-trash-alt"></i></td>\
                            </tr>'
        }, _settings);

        // 自定義函式
        var coreMethod = {
            initial: function() {
                $('table', settings.component)
                    .attr('id', settings.id)
                    .attr('class', settings.class)
                    .attr('name', settings.name);

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
                // datalist 選中之後，會自動被置制到 input，這時再偵測，如果input內容有在 datalist裡的話，就將datalist的內容刪除
                $('button.add_field', settings.component).click(function() {
                    $('table tbody', settings.component).append(settings.emptyItem);

                    $('table#' + settings.id + ' tbody').sortable({
                        handle: "td i.fa-grip-vertical"
                    });

                });

                // 刪除項目
                $('body').on('click', 'table#' + settings.id + ' button[name="delete"]', function() {
                    $(this).closest('tr').remove();
                });



            },
            getName: function() {
                return settings.name;
            },
            getValue: function() {
                var items = [];
                $('tbody tr', settings.component).each(function() {
                    var item = {};
                    item.icon = $('input[name="icon"]', $(this)).val();
                    item.name = $('input[name="name"]', $(this)).val();
                    item.widget = $('input[name="widget"]', $(this)).val();
                    item.linkfield = $('input[name="linkfield"]', $(this)).val();

                    items.push(item);
                });

                return JSON.stringify(items);
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
                    value = '[]';
                }

                $('table tbody tr', settings.component).remove();

                var items = JSON.parse(value);
                for (var i in items) {
                    var item = $(settings.emptyItem).clone();

                    $('input[name="icon"]', item).val(items[i].icon);
                    $('input[name="name"]', item).val(items[i].name);
                    $('input[name="widget"]', item).val(items[i].widget);
                    $('input[name="linkfield"]', item).val(items[i].linkfield);

                    $('table tbody', settings.component).append(item);
                }

                $('table#' + settings.id + ' tbody').sortable({
                    handle: "td i.fa-grip-vertical"
                });
            },
            showInList: function(value) {
                return value;
            }
        };

        return coreMethod;
    };
}(jQuery));