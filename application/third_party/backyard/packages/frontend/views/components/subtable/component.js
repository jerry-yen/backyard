(function($) {

    /**
     * 子清單元件
     * 
     * @param {*} _settings 設定值
     */
    $.subtable_component = function(_settings) {
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
                                <th>操作</th>\
                            </tr>\
                        </thead>\
                        <tbody>\
                        </tbody>\
                    </table>\
                </div>'),
            'emptyItem': $('\
                            <tr>\
                                <td><i class="fas fa-grip-vertical"></i></td>\
                                <td>\
                                    <button type="button" name="modify" class="btn bg-blue"><i class="fas fa-pen"></i>\
                                    <button type="button" name="delete" class="btn bg-red"><i class="fas fa-trash-alt"></i>\
                                </td>\
                            </tr>'),
            'dialog': $('<div class="modal fade">\
                            <div class="modal-dialog modal-lg">\
                                <div class="modal-content">\
                                    <div class="modal-header bg-info">\
                                        <h4 class="modal-title"></h4>\
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">\
                                            <span aria-hidden="true">&times;</span>\
                                        </button>\
                                    </div>\
                                    <div class="modal-body">\
                                        <form role="form">\
                                            <div class="card-body">\
                                            </div>\
                                            <!-- /.card-body -->\
                                        </form>\
                                    </div>\
                                    <div class="modal-footer justify-content-between">\
                                        <button type="button" class="btn btn-default" data-dismiss="modal">關閉</button>\
                                        <button type="button" class="btn btn-primary save-item">儲存</button>\
                                    </div>\
                                </div>\
                                <!-- /.modal-content -->\
                            </div>\
                            <!-- /.modal-dialog -->\
                        </div>\
                        <!-- /.modal -->\
        ')
        }, _settings);

        var components = [];
        var data = {};
        var fields = [];

        // 自定義函式
        var coreMethod = {
            initial: function() {
                $('table', settings.component)
                    .attr('id', settings.id)
                    .attr('class', settings.class)
                    .attr('name', settings.name);

                settings.dialog.attr('id', settings.id + '-modal');
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

                // 取得組件代碼
                var widgetCode = '';
                var elements = settings.source.split(';')
                for (var key in elements) {
                    var response = elements[key].match(/widget\{(.*?)\}/i);
                    if (response != null) {
                        widgetCode = response[1];
                        break;
                    }
                };

                // 找不到組件代碼則離開
                if (widgetCode.trim() == '') {
                    return;
                }

                var backyard = $.backyard({ 'userType': settings.userType });

                // 取得組件的後設資訊
                var widgetMetadata = backyard.metadata.widget(widgetCode);
                // console.log(widgetMetadata);

                var container = $('table tbody', settings.component);

                // 清單欄位
                fields = widgetMetadata.metadata.widget.listfields;
                var headContainer = $('table thead tr', settings.component);

                for (var key in fields) {
                    if (fields[key].status != 'Y') {
                        continue;
                    }
                    headContainer.append($('<th>' + fields[key].name + '</th>'));
                    settings.emptyItem.append($('<td class="' + key + '"></td>'));
                }

                // Modal 標題
                $('h4.modal-title', settings.dialog).html(widgetMetadata.metadata.name);


                // 開啟新增表單Modal
                $('button.add_field', settings.component).click(function() {
                    // 繪製表單欄位
                    data['id'] = undefined;
                    components = backyard.html.renderForm(widgetMetadata.metadata.dataset, $('div.modal-body div.card-body', settings.dialog));
                    settings.dialog.modal('toggle');
                });

                // 修改資料
                $('body').off('click', 'table#' + settings.id + ' button[name="modify"]');
                $('body').on('click', 'table#' + settings.id + ' button[name="modify"]', function() {
                    var item = $(this).closest('tr');
                    data = JSON.parse(item.attr('data'));

                    components = backyard.html.renderForm(widgetMetadata.metadata.dataset, $('div.modal-body div.card-body', settings.dialog));
                    for (var key in components) {
                        components[key].setValue(data[key]);
                    }

                    settings.dialog.modal('toggle');
                });

                // 刪除資料
                $('body').off('click', 'table#' + settings.id + ' button[name="delete"]');
                $('body').on('click', 'table#' + settings.id + ' button[name="delete"]', function() {
                    console.log('delete');
                });

                // 儲存鈕事件
                $('body').off('click', '#' + settings.id + '-modal button.save-item');
                $('body').on('click', '#' + settings.id + '-modal button.save-item', function() {

                    // 取得所有元件欄位中的值

                    for (var key in components) {
                        data[components[key].getName()] = components[key].getValue();
                        components[key].setInvalid('');
                    }

                    var isCreate = false;
                    if (data['id'] == undefined || data['id'] == '') {
                        data['id'] = coreMethod.getUUID();
                        isCreate = true;
                        item = settings.emptyItem.clone();
                        item.attr('id', data['id']);
                    } else {
                        item = $('tr[id="' + data['id'] + '"]', settings.component);
                    }

                    // 如果按儲存的話，就做有改變的記號
                    data['changed'] = 'Y';

                    // 將所有欄位值暫時打包成 JSON 格式，並先儲存在 tr 的 data 屬性中
                    var itemJSON = JSON.stringify(data);
                    item.attr('data', itemJSON);

                    // 跑一次清單中有顯示哪些欄位，把值丟給指定欄位
                    for (var key in fields) {
                        if (fields[key].status != 'Y') {
                            continue;
                        }

                        $('td.' + key, item).html(data[key]);
                    }

                    // 新增
                    if (isCreate) {
                        // 新增欄位
                        container.append(item);
                    }

                    // 關閉表單Modal
                    settings.dialog.modal('toggle');

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
                    var item = JSON.parse($(this).attr('data'));
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

                $('table tbody tr', settings.component).remove();

                if (value == undefined || value == '') {
                    value = [];
                }

                var items = value;
                for (var i in items) {
                    var item = $(settings.emptyItem).clone();
                    item.attr('id', items[i]['id']);
                    item.attr('data', JSON.stringify(items[i]));
                    for (var key in fields) {
                        if (fields[key].status != 'Y') {
                            continue;
                        }

                        $('td.' + key, item).html(items[i][key]);
                    }

                    $('table tbody', settings.component).append(item);
                }

                $('table#' + settings.id + ' tbody').sortable({
                    handle: "td i.fa-grip-vertical"
                });
            },
            getUUID() {
                return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                    var r = Math.random() * 16 | 0,
                        v = c == 'x' ? r : r & 0x3 | 0x8;
                    return v.toString(16);
                }).toUpperCase();

            },
            showInList: function(value) {
                return value;
            }
        };

        return coreMethod;
    };
}(jQuery));