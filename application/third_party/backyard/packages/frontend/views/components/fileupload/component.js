(function($) {

    /**
     * 檔案上傳元件
     * 
     * @param {*} _settings 設定值
     */
    $.fileupload_component = function(_settings) {
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
                    <label for="" class="btn bg-green float-right">\
                        <i class="fas fa-plus"></i> 新增檔案\
                        <input id="" type="file" multiple style="display:none;">\
                    </label>\
                    <table class="table table-hover text-nowrap">\
                        <thead>\
                            <tr>\
                                <th>&nbsp;</th>\
                                <th>檔案名稱</th>\
                                <th>檔案大小</th>\
                                <th>上傳時間</th>\
                                <th>操作</th>\
                            </tr>\
                        </thead>\
                        <tbody>\
                        </tbody>\
                    </table>\
                </div>\
            '),
            'emptyItem': $('\
                <tr>\
                    <td class="drop"><div class="sort-drop"><i class="fas fa-grip-vertical"></i></div></td>\
                    <td class="filename"></td>\
                    <td class="filesize"></td>\
                    <td class="createTime">\
                        <div class="progress">\
                            <div class="progress-bar bg-primary progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">\
                                <span class="sr-only">40% Complete (success)</span>\
                            </div>\
                        </div>\
                    </td>\
                    <td class="op">\
                        <button type="button" class="delete btn bg-red"><i class="fas fa-trash-alt"></i></button>\
                    </div>\
                </td>\
                </tr>\
            ')
        }, _settings);

        // 自定義函式
        var coreMethod = {
            initial: function() {
                settings.component
                    .attr('id', settings.id);

                $('input', settings.component)
                    .attr('id', settings.id + '_file')
                    .val(settings.value)
                    .attr('class', settings.class)
                    .attr('name', settings.name);

                $('label', settings.component)
                    .attr('for', settings.id + '_file')
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
                $('document').ready(function() {
                    $('input[name="' + settings.name + '"]').fileupload({
                        url: '/index.php/api/file/user/' + settings.userType + '/code/' + settings.code + '/field/' + settings.name,
                        // 每個檔案都會乎叫一次 add
                        add: function(e, data) {
                            var item = settings.emptyItem.clone();
                            $('td.filename', item).html(data.files[0].name);
                            $('td.filesize', item).html(data.files[0].size);
                            $('table tbody', settings.component).append(item);
                            item.attr('filename', data.files[0].name);
                            data.submit();

                            $('table tbody', settings.component).sortable({
                                handle: "td i.fa-grip-vertical"
                            });
                        },
                        progress: function(e, data) {
                            var percentage = parseInt(data.loaded / data.total * 100);
                            var progress = $('tr[filename="' + data.files[0].name + '"] div.progress-bar', settings.component);
                            progress.attr('aria-valuenow', percentage);
                            progress.css('width', percentage + '%');
                        },
                        done: function(e, data) {
                            var createTime = $('tr[filename="' + data.files[0].name + '"] td.createTime', settings.component);
                            createTime.html(data.result.file.created_at);
                            $('tr[filename="' + data.files[0].name + '"]', settings.component).attr('file', JSON.stringify(data.result.file));
                        },
                    });
                });

                $('body').on('click', '#' + settings.id + ' table tr button.delete', function() {
                    $(this).closest('tr').remove();
                });

            },
            getName: function() {
                return settings.name;
            },
            getValue: function() {
                var files = [];
                $('tbody tr', settings.component).each(function() {
                    files.push(JSON.parse($(this).attr('file')));
                });
                return JSON.stringify(files);
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
                    value = [];
                }
                for (var key in value) {
                    var item = settings.emptyItem.clone();
                    item.attr('filename', value[key].name);
                    item.attr('file', JSON.stringify(value[key]));
                    $('td.filename', item).html(value[key].name);
                    $('td.filesize', item).html(value[key].file_size);
                    $('td.createTime', item).html(value[key].created_at);
                    $('table tbody', settings.component).append(item);

                }

                $('table tbody', settings.component).sortable({
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