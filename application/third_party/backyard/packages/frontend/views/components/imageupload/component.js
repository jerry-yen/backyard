(function($) {

    /**
     * 圖片上傳元件
     * 
     * @param {*} _settings 設定值
     */
    $.imageupload_component = function(_settings) {
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
                    <label for="" class="new-image">\
                        <div><i class="fas fa-plus-circle"></i></div>\
                        <input id="" type="file" multiple style="display:none;">\
                    </label>\
                    <div class="images">\
                    </div>\
                    <div class="clearfix"></div>\
                </div>\
            '),
            'emptyItem': $('\
            <div class="image">\
                <button class="file-delete btn bg-red"><i class="fas fa-times"></i></button>\
                <div class="image-contain">\
                    <img src="">\
                    <div class="progress d-none"></div>\
                </div>\
            </div>\
            '),
            'listItem': $('<div class="image-in-list">\
                <div class="image-contain">\
                    <img src="">\
                </div>\
            </div>\
        ')
        }, _settings);

        // 自定義函式
        var coreMethod = {
            initial: function() {
                settings.component
                    .attr('id', settings.id);

                $('input', settings.component)
                    .attr('id', settings.id + '_image')
                    .val(settings.value)
                    .attr('class', settings.class)
                    .attr('name', settings.name);

                $('label', settings.component)
                    .attr('for', settings.id + '_image')
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

                    //$('div.image').image_fit();

                    $('input[name="' + settings.name + '"]').fileupload({
                        url: '/api/file/field/' + settings.name + '?userType=' + settings.userType + '&code=' + settings.code,
                        // 每個檔案都會乎叫一次 add
                        add: function(e, data) {
                            var item = settings.emptyItem.clone();
                            $('div.images', settings.component).append(item);
                            item.attr('filename', data.files[0].name);

                            var reader = new FileReader();
                            reader.onload = function(e) {
                                $('img', item).attr('src', e.target.result);
                            };
                            reader.readAsDataURL(data.files[0]);
                            data.submit();

                            $('div.images', settings.component).sortable({
                                // handle: "td i.fa-grip-vertical"
                            });
                        },
                        progress: function(e, data) {
                            var percentage = parseInt(data.loaded / data.total * 100);
                            var progress = $('div.image[filename="' + data.files[0].name + '"] div.progress', settings.component);
                            progress.css('width', percentage + '%');
                            if (percentage >= 100) {
                                progress.addClass('d-none');
                            } else {
                                progress.removeClass('d-none');
                            }
                        },
                        done: function(e, data) {
                            $('div.image[filename="' + data.files[0].name + '"]', settings.component).attr('file', JSON.stringify(data.result.file));
                        },
                    });
                });

                $('body').on('click', '#' + settings.id + ' div.images div.image button.file-delete', function() {
                    $(this).closest('div.image').remove();
                });

            },
            getName: function() {
                return settings.name;
            },
            getValue: function() {
                var files = [];
                $('div.images div.image', settings.component).each(function() {
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

                $('div.image', settings.component).remove();

                for (var key in value) {
                    var item = settings.emptyItem.clone();
                    item.attr('file', JSON.stringify(value[key]));
                    $('img', item).attr('src', '/uploads/' + value[key].path);
                    $('div.images', settings.component).append(item);
                }

                $('div.images', settings.component).sortable({
                    //handle: "td i.fa-grip-vertical"
                });
            },
            showInList: function(value) {
                if (value.length == 0) {
                    return '無';
                }
                var item = settings.listItem.clone();
                for (var key in value) {
                    $('img', item).attr('src', '/uploads/' + value[key].path);
                    break;
                }
                return item;
            }
        };

        return coreMethod;
    };
}(jQuery));