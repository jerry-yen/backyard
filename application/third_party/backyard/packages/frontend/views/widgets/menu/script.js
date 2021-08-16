(function($) {

    /**
     * 選單組件
     * 
     * @param {*} _settings 設定值
     */
    $.fn.widget_menu = function(_settings) {
        var settings = $.extend({
            'userType': 'admin',
            'code': $(this).attr('widget'),
            'instance': this,
            'uri': '',
        }, _settings);

        // 自定義函式
        var widget = {

            menu: {
                initial: function() {

                    var metadata = $backyard.template.widgets[settings.code];
                    console.log(metadata);
                    for (var i in metadata.setting.menu) {
                        var item = $('li.nav-item.template.d-none', settings.instance).eq(0).clone();
                        item.removeClass('template').removeClass('d-none');
                        item = widget.menu.option.subItem(metadata.setting.menu[i], item, 1);
                        $('ul.nav:first', settings.instance).append(item);
                    }

                    // 如該頁是選單所屬的頁面，則展開所有上層的選單

                    $('a.active').parents('ul').each(function() {
                        $(this).css({ 'display': 'block' });
                        $(this).closest('li').addClass('menu-open');
                    });
                    $('[data-widget="treeview"]', settings.instance).Treeview('init');

                },

                loadData: function() {

                },

                listener: {

                },

                option: {
                    subItem: function(menu, item, level) {
                        if (menu.icon.trim() != '') {
                            $('i', item).attr('class', menu.icon);
                        }

                        // $('a:first', item).css({ 'padding-left': (level * 12) + 'px' });
                        if (menu.type == 'page') {
                            $('p', item).html(menu.title);
                            $('a:first', item).attr('href', '/index.php/' + menu.uri);
                            if (menu.uri == settings.uri) {
                                $('a:first', item).addClass('active');
                            }
                            return item;
                        } else {
                            $('p', item).html(menu.title + '<i class="right fas fa-angle-left"></i>');
                            item.append('<ul class="nav nav-treeview"></ul>');
                            for (var i in menu.subItems) {
                                var subItem = $('li.nav-item.template.d-none', settings.instance).eq(0).clone();
                                subItem.removeClass('template').removeClass('d-none');
                                subItem = this.subItem(menu.subItems[i], subItem, level + 1);
                                $('ul.nav:first', item).append(subItem);
                            }
                            return item;

                        }
                    }
                }
            }

        };

        widget.menu.initial();
        return widget;

    };
}(jQuery));