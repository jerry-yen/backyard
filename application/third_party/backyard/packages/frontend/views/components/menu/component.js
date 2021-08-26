(function($) {

    /**
     * 選單編輯元件
     * 
     * @param {*} _settings 設定值
     */
    $.menu_component = function(_settings) {
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
                    <div class="toolbar float-right">\
                        <button type="button" name="add_class" class="btn bg-green"><i class="fas fa-th-list"></i></button>\
                        <button type="button" name="add_page" class="btn bg-blue"><i class="far fa-file"></i></button>\
                    </div>\
                    <div class="clearfix"></div>\
                    <div class="root">\
                    \
                    </div>\
                </div>\
            '),
            'emptyClass': $('\
                <div class="item pageClass" style="margin: 20px 0px;background-color:#d0d3ef;padding:10px;border: 1px solid black;">\
                    <div class="label" title="分類" style="display: inline;padding: 7px;background-color: #d0d3ef;"><i class="fas fa-th-list"></i></div>\
                    <input type="text" name="icon" class="form-control" placeholder="ICON" style="width:10%;display:inline;">\
                    <input type="text" name="title" class="form-control" placeholder="分類名稱" style="width:60%;display:inline;">\
                    <button type="button" name="add_class" class="btn bg-green" title="新增分類"><i class="fas fa-th-list"></i></button>\
                    <button type="button" name="add_page" class="btn bg-blue" title="新增頁面"><i class="far fa-file"></i></button>\
                    <button type="button" name="delete" class="btn bg-red" title="刪除"><i class="fas fa-trash"></i></button>\
                </div>\
            '),
            'emptyItem': $('\
                <div class="item page" style="margin: 20px 0px;background-color:#444d73;padding:10px;border: 1px solid black;">\
                <div class="label" title="分類" style="display: inline;padding: 7px;background-color: #444d73;"><i class="far fa-file""></i></div>\
                    <input type="text" name="icon" placeholder="ICON" class="form-control" style="width:10%;display:inline;">\
                    <select class="form-control" style="width:70%;display:inline;"></select>\
                    <button type="button" name="delete" class="btn bg-red" title="刪除"><i class="fas fa-trash"></i></button>\
                </div>\
            ')
        }, _settings);

        // 自定義函式
        var coreMethod = {
            initial: function() {
                settings.component
                    .attr('id', settings.id)
                    // .attr('class', settings.class)
                    .attr('name', settings.name)
                    .val(settings.value);


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
                $backyard.utility.api(
                    '/api/metadatas/page',
                    settings.params,
                    'GET',
                    function(response) {
                        for (var key in response.items) {
                            $('select', settings.emptyItem).append('<option value="' + response.items[key].code + '" uri="' + response.items[key].uri + '">' + response.items[key].name + '</option>');
                        }
                        // settings.component.append(settings.emptyItem);
                    },
                    null,
                    'JSON',
                    false
                );

                // 新增分類
                $('body').on('click', '#' + settings.id + ' div.toolbar button[name="add_class"]', function() {
                    var newClass = settings.emptyClass.clone();
                    newClass.attr('level', 1);
                    $('div.root', settings.component).append(newClass);
                });

                // 新增頁面
                $('body').on('click', '#' + settings.id + ' div.toolbar button[name="add_page"]', function() {
                    var newPage = settings.emptyItem.clone();
                    newPage.attr('level', 1);
                    $('div.root', settings.component).append(newPage);
                });

                // 項目中的新增分類
                $('body').on('click', '#' + settings.id + ' div.item button[name="add_class"]', function() {
                    var parent = $(this).closest('div.item');
                    if ($('div.list', parent).length == 0) {
                        parent.append('<div class="list" style="margin-left:55px;"></div>');
                    }
                    var newClass = settings.emptyClass.clone();
                    var level = parseInt(parent.attr('level')) + 1;
                    newClass.attr('level', level);
                    $('div.list', parent).eq(0).append(newClass);
                    $('div.root, div.list', settings.component).sortable();
                });

                // 項目中的新增頁面
                $('body').on('click', '#' + settings.id + ' div.item button[name="add_page"]', function() {
                    var parent = $(this).closest('div.item');
                    if ($('div.list', parent).length == 0) {
                        parent.append('<div class="list" style="margin-left:55px;"></div>');
                    }
                    var newPage = settings.emptyItem.clone();
                    var level = parseInt(parent.attr('level')) + 1;
                    newPage.attr('level', level);
                    $('div.list', parent).eq(0).append(newPage);
                    $('div.root, div.list', settings.component).sortable();
                });

                // 項目中的新增頁面
                $('body').on('click', '#' + settings.id + ' div.item button[name="delete"]', function() {
                    var parent = $(this).closest('div.item');
                    parent.remove();
                });

                $('div.root, div.list', settings.component).sortable();

            },
            getName: function() {
                return settings.name;
            },
            getValue: function() {

                // 第一層

                var level_1 = [];
                $('.root > .item', settings.component).each(function() {
                    // 第二層
                    var level_2 = [];
                    $('.list > .item[level="2"]', $(this)).each(function() {
                        // 第三層
                        var level_3 = [];
                        $('.list > .item[level="3"]', $(this)).each(function() {
                            level_3.push({
                                'type': $(this).hasClass('pageClass') ? 'pageClass' : 'page',
                                'icon': $('input[name="icon"]', $(this)).val(),
                                'title': $(this).hasClass('pageClass') ? $('input[name="title"]', $(this)).val() : $('select option:selected', $(this)).text(),
                                'code': $(this).hasClass('pageClass') ? '' : $('select', $(this)).val(),
                                'uri': $(this).hasClass('pageClass') ? '' : $('select option:selected', $(this)).attr('uri')
                            });


                        });

                        var item = {
                            'type': $(this).hasClass('pageClass') ? 'pageClass' : 'page',
                            'icon': $('input[name="icon"]', $(this)).val(),
                            'title': $(this).hasClass('pageClass') ? $('input[name="title"]', $(this)).val() : $('select option:selected', $(this)).text(),
                            'code': $(this).hasClass('pageClass') ? '' : $('select', $(this)).val(),
                            'uri': $(this).hasClass('pageClass') ? '' : $('select option:selected', $(this)).attr('uri')
                        };

                        if ($(this).hasClass('pageClass')) {
                            item.subItems = level_3;
                        }
                        level_2.push(item);
                    });

                    var item = {
                        'type': $(this).hasClass('pageClass') ? 'pageClass' : 'page',
                        'icon': $('input[name="icon"]', $(this)).val(),
                        'title': $(this).hasClass('pageClass') ? $('input[name="title"]', $(this)).val() : $('select option:selected', $(this)).text(),
                        'code': $(this).hasClass('pageClass') ? '' : $('select', $(this)).val(),
                        'uri': $(this).hasClass('pageClass') ? '' : $('select option:selected', $(this)).attr('uri')
                    };

                    if ($(this).hasClass('pageClass')) {
                        item.subItems = level_2;
                    }
                    level_1.push(item);
                });


                return level_1;
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
            setValue: function(items) {
                if (items == undefined || items == '') {
                    items = [];
                }
                items = this.loadItem(items, 1);
                for (var key in items) {
                    $('div.root', settings.component).append(items[key]);
                }
            },
            loadItem: function(items, level) {
                var byard_items = [];
                for (var key in items) {
                    if (items[key].type == 'pageClass') {
                        var subItems = this.loadItem(items[key].subItems, level + 1);
                        var item = settings.emptyClass.clone();
                        item.attr('level', level);
                        $('input[name="title"]', item).val(items[key].title);
                        $('input[name="icon"]', item).val(items[key].icon);
                        if ($('div.list', item).length == 0) {
                            item.append('<div class="list" style="margin-left:55px;"></div>')
                        }
                        for (var subKey in subItems) {
                            $('div.list', item).eq(0).append(subItems[subKey]);
                        }
                    } else if (items[key].type == 'page') {
                        var item = settings.emptyItem.clone();
                        item.attr('level', level);
                        $('select', item).val(items[key].code);
                        /*
                        $('select option', item).filter(function () {
                            return this.text == items[key].title;
                        }).prop('selected', true);
                        */
                        $('input[name="icon"]', item).val(items[key].icon);
                    }

                    byard_items.push(item);
                }

                return byard_items;
            },
            showInList: function(value) {
                return value;
            }
        };

        return coreMethod;
    };
}(jQuery));