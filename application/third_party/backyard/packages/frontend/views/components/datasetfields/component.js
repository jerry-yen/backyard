(function($) {

    /**
     * 資料集欄位元件
     * 
     * @param {*} _settings 設定值
     */
    $.datasetfields_component = function(_settings) {
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
                                <th>名稱</th>\
                                <th>前端變數</th>\
                                <th>資料庫變數</th>\
                                <th>元件</th>\
                                <th>驗證</th>\
                                <th>轉換</th>\
                                <th>資料來源</th>\
                                <th>提示</th>\
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
                                <td><input type="text" name="name" class="form-control field_component"></td>\
                                <td><input type="text" name="frontendVariable" class="form-control"></td>\
                                <td><input type="text" name="dbVariable" class="form-control"></td>\
                                <td>\
                                    <select name="component" class="form-control">\
                                        <optgroup label="HTML元件">\
                                            <option value="text">文字方塊</option>\
                                            <option value="textarea">多行文字</option>\
                                            <option value="number">數字</option>\
                                            <option value="date">日期</option>\
                                            <option value="select">單選下拉</option>\
                                            <option value="label">標籤</option>\
                                            <option value="hidden">隱藏</option>\
                                        </optgroup>\
                                        <optgroup label="區塊元件">\
                                            <option value="grouplabel">群組標籤</option>\
                                        </optgroup>\
                                        <optgroup label="jQuery元件">\
                                            <option value="switch">開關閘</option>\
                                            <option value="fileupload">檔案上傳</option>\
                                            <option value="imageupload">圖片上傳</option>\
                                            <option value="multiselect">多選下拉</option>\
                                            <option value="autocomplete">自動完成</option>\
                                        </optgroup>\
                                        <optgroup label="其他元件">\
                                            <option value="htmleditor">HTML編輯器</option>\
                                            <option value="subtable">子清單</option>\
                                        </optgroup>\
                                    </select>\
                                </td>\
                                <td>\
                                    <div class="datalist">\
                                        <select name="validatorlist" class="form-control">\
                                            <option value="">選擇即刪除</option>\
                                        </select>\
                                        <input name="validator" class="form-control" placeholder="0項">\
                                    </div>\
                                </td>\
                                <td>\
                                    <div class="datalist">\
                                        <select name="converterlist" class="form-control">\
                                            <option value="">選擇即刪除</option>\
                                        </select>\
                                        <input name="converter" class="form-control" placeholder="0項">\
                                    </div>\
                                </td>\
                                <td><input type="text" name="source" class="form-control"></td>\
                                <td><input type="text" name="fieldTip" class="form-control"></td>\
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

                    $('input[name="validator"], input[name="converter"]', settings.component).each(function() {
                        var count = $('select option', $(this).closest('td')).length;
                        $(this).attr('placeholder', (count - 1) + '項');
                    });

                    $('table#' + settings.id + ' tbody').sortable({
                        handle: "td i.fa-grip-vertical"
                    });

                });

                // 新增 datalist 資料
                $('body').on('keypress', 'table#' + settings.id + ' input[name="validator"], input[name="converter"]', function(event) {
                    if (event.keyCode != 13) {
                        return;
                    }

                    $('select', $(this).closest('td')).append($('<option>').attr('value', $(this).val()).text($(this).val()));
                    $(this).val('');
                    $(this).focus();

                    var count = $('select option', $(this).closest('td')).length;
                    $(this).attr('placeholder', (count - 1) + '項');
                });

                // 刪除 datalist 資料
                $('body').on('change', 'table#' + settings.id + ' div.datalist select', function() {
                    $('option[value="' + $(this).val() + '"]', $(this)).remove();
                    var count = $('select option', $(this).closest('td')).length;
                    $('input', $(this).closest('td')).attr('placeholder', (count - 1) + '項');
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
                    item.name = $('input[name="name"]', $(this)).val();
                    item.frontendVariable = $('input[name="frontendVariable"]', $(this)).val();
                    item.dbVariable = $('input[name="dbVariable"]', $(this)).val();
                    item.component = $('select[name="component"]', $(this)).val();
                    item.source = $('input[name="source"]', $(this)).val();
                    item.fieldTip = $('input[name="fieldTip"]', $(this)).val();
                    item.validator = [];
                    item.converter = [];
                    $('select[name="validatorlist"] option', $(this)).each(function(index) {
                        if (index > 0) {
                            item.validator.push($(this).attr('value'));
                        }
                    });

                    $('select[name="converterlist"] option', $(this)).each(function(index) {
                        if (index > 0) {
                            item.converter.push($(this).attr('value'));
                        }
                    });

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

                    $('input[name="name"]', item).val(items[i].name);
                    $('input[name="frontendVariable"]', item).val(items[i].frontendVariable);
                    $('input[name="dbVariable"]', item).val(items[i].dbVariable);
                    $('select[name="component"]', item).val(items[i].component);

                    if (items[i].validator.length > 0) {
                        for (var v in items[i].validator) {
                            $('select[name="validatorlist"]', item).append('<option value="' + items[i].validator[v] + '">' + items[i].validator[v] + '</option>');
                        }
                        $('input[name="validator"]', item).attr('placeholder', (items[i].validator.length) + '項');
                    }
                    if (items[i].converter.length > 0) {
                        for (var c in items[i].converter) {
                            $('select[name="converterlist"]', item).append('<option value="' + items[i].converter[c] + '">' + items[i].converter[c] + '</option>');
                        }
                        $('input[name="converter"]', item).attr('placeholder', (items[i].converter.length) + '項');
                    }

                    $('input[name="source"]', item).val(items[i].source);
                    $('input[name="fieldTip"]', item).val(items[i].fieldTip);


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