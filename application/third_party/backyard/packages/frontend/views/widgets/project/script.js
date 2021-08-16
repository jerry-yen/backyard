(function($) {

    /**
     * 專案組件
     * 
     * @param {*} _settings 設定值
     */
    $.fn.backyard_project = function(_settings) {
        var code = $(this).attr('widget');
        var settings = $.extend({
            'userType': 'admin',
            'code': code,
            'instance': this,
            'params': { 'parent_id': '' },

            'project_add_button_selector': 'div[widget="' + code + '"] button.add-project',
            'project_modify_button_selector': 'div[widget="' + code + '"] span.modify-project',
            'project_delete_button_selector': 'div[widget="' + code + '"] span.delete-project',
            'project_save_button_selector': 'div[widget="' + code + '"] button.save-project',
            'project_list_button_selector': 'div[widget="' + code + '"] li.nav-item a.nav-link',

            'item_add_button_selector': 'div[widget="' + code + '"] button.add-item',
            'item_modify_button_selector': 'div[widget="' + code + '"] button.modify-item',
            'item_delete_button_selector': 'div[widget="' + code + '"] button.delete-item',
            'item_save_button_selector': 'div[widget="' + code + '"] button.save-item',

        }, _settings);

        var backyard = $.backyard({ 'userType': settings.userType });


        // 自定義函式
        var widget = {

            /**
             * 專案
             */
            project: {

                /**
                 * 專案欄位元件
                 */
                components: [],

                /**
                 * 初始化
                 */
                initial: function() {

                    // 載入專案清單資料
                    this.action.list();

                    // 繪出表單欄位
                    this.action.renderForm();

                    // 新增事件
                    widget.project.listener.add();
                    // 修改事件
                    widget.project.listener.modify();
                    // 刪除事件
                    widget.project.listener.delete();
                    // 儲存
                    widget.project.listener.save();
                    // 載入項目
                    widget.project.listener.sublist();

                },

                /**
                 * 可執行的動作
                 */
                action: {

                    /**
                     * 條列專案清單
                     */
                    list: function() {
                        var container = $('ul.project-list', settings.instance);
                        var template = $('li.nav-item.d-none', container).clone();

                        // 清空清單
                        $('li.nav-item', container).not('.d-none, .sprint').remove();

                        backyard.data.list('project', function(project, key) {
                            var item = template.clone();
                            item.attr('id', project.id);
                            $('span.title', item).html(project.title);
                            $('span.progress', item).html(project.progress);
                            item.removeClass('d-none');
                            container.append(item);
                        });
                    },

                    /**
                     * 繪出表單欄位
                     */
                    set: function(id) {
                        backyard.data.set('project', id, function(project) {
                            for (var field in project) {
                                if (widget.project.components[field] != undefined) {
                                    widget.project.components[field].setValue(project[field]);
                                }
                            }

                            var container = $('#project-info div.modal-body div.card-body', settings.instance);

                            // 如果預設有id，代表為修改模式
                            if (project.id != undefined && $('input[name="id"]', container).length == 0) {
                                container.append('<input type="hidden" name="id" value="' + project.id + '"/>')
                            } else {
                                $('input[name="id"]', container).val(project.id);
                            }
                        });
                    },

                    renderForm: function() {
                        widget.project.components = backyard.html.renderForm('project', $('#project-info div.modal-body div.card-body'));
                    }
                },

                /**
                 * 事件
                 */
                listener: {

                    /**
                     * 按下新增鈕
                     */
                    add: function() {
                        $('body').off('click', settings.project_add_button_selector);
                        $('body').on('click', settings.project_add_button_selector, function() {
                            widget.project.action.renderForm();
                            $('#project-info').modal('toggle');
                        });
                    },

                    /**
                     * 按下修改鈕
                     */
                    modify: function() {
                        $('body').off('click', settings.project_modify_button_selector);
                        $('body').on('click', settings.project_modify_button_selector, function() {
                            var id = $(this).closest('li.nav-item').attr('id');
                            widget.project.action.set(id);
                            $('#project-info').modal('toggle');
                        });
                    },

                    /**
                     * 按下刪除鈕
                     */
                    delete: function() {
                        $('body').off('click', settings.project_delete_button_selector);
                        $('body').on('click', settings.project_delete_button_selector, function() {

                            var id = $(this).closest('li.nav-item').attr('id');

                            backyard.dialog.confirm(
                                '刪除專案',
                                '請注意！確定刪除後專案內的所有資料將<u>無法還原</u>',
                                'warning',
                                function(result) {
                                    if (result.value) {
                                        backyard.data.delete('project', id, function() {
                                            widget.project.action.list();
                                        });
                                    }
                                },
                                '刪除',
                                '取消'
                            );
                        });
                    },

                    /**
                     * 按下表單儲存鈕
                     */
                    save: function() {
                        $('body').off('click', settings.project_save_button_selector);
                        $('body').on('click', settings.project_save_button_selector, function() {

                            var id_component = $('#project-info div.modal-body div.card-body input[name="id"]', settings.instance);

                            id = (id_component.length > 0) ? id_component.val() : '';
                            if (backyard.data.save(
                                    'project',
                                    id,
                                    widget.project.components
                                )) {
                                backyard.dialog.alert('儲存成功', '', 'success');
                                widget.project.action.list();
                                $('#project-info').modal('toggle');
                            }
                        });
                    },

                    sublist: function() {
                        $('body').off('click', settings.project_list_button_selector);
                        $('body').on('click', settings.project_list_button_selector, function() {
                            var container = $(this).closest('li.nav-item');
                            var id = container.attr('id');
                            widget.todolist.action.list(id);

                            var projectTitle = $('span.title', container).html();
                            $('span.project-title').html(projectTitle);
                        });
                    }

                }
            },

            todolist: {

                components: [],

                parentId: '',

                initial: function() {

                    var firstItem = $('div.project-list li.nav-item').not('.d-none').eq(0);
                    var projectTitle = $('span.title', firstItem).html();
                    $('span.project-title').html(projectTitle);
                    this.action.list('sprint');

                    this.action.renderForm();


                    // 新增事件
                    widget.todolist.listener.add();
                    // 修改事件
                    widget.todolist.listener.modify();
                    // 刪除事件
                    widget.todolist.listener.delete();
                    // 儲存
                    widget.todolist.listener.save();
                },

                action: {
                    /**
                     * 條列專案清單
                     */
                    list: function(projectId) {
                        var container = $('div.todolist-view table tbody', settings.instance);
                        var template = $('tr.nav-item.d-none', container).clone();

                        widget.todolist.parentId = projectId;
                        var params = {};
                        if (projectId == 'sprint') {
                            params = { 'sprint': 'Y' };
                        } else {
                            params = { 'parent_id': projectId };
                        }


                        // 清空清單
                        $('tr.nav-item', container).not('.d-none').remove();

                        backyard.data.list('item', function(data, key) {
                            var item = template.clone();
                            item.attr('id', data.id);
                            $('td.title', item).html(data.title);
                            //$('td.task-progress', item).html(data.title);
                            $('td.deadline', item).html(data.deadline);
                            $('td.task-progress', item).html(data.progress);
                            if (data.sprint == 'Y') {
                                $('button.sprint', item).removeClass('bg-gray').addClass('bg-red');
                            } else {
                                $('button.sprint', item).removeClass('bg-red').addClass('bg-gray');
                            }

                            if (widget.todolist.parentId == 'sprint') {
                                $('button.delete-item', item).addClass('d-none');
                            }

                            item.removeClass('d-none');
                            container.append(item);
                        }, params);

                        if (widget.todolist.parentId == 'sprint') {
                            $('div.todolist-view button.add-item').addClass('d-none');
                        } else {
                            $('div.todolist-view button.add-item').removeClass('d-none');
                        }

                    },

                    /**
                     * 繪出表單欄位
                     */
                    set: function(id) {
                        backyard.data.set('item', id, function(item) {
                            for (var field in item) {
                                if (widget.todolist.components[field] != undefined) {
                                    widget.todolist.components[field].setValue(item[field]);
                                }
                            }

                            var container = $('#item-info div.modal-body div.card-body', settings.instance);

                            // 如果預設有id，代表為修改模式
                            if (item.id != undefined && $('input[name="id"]', container).length == 0) {
                                container.append('<input type="hidden" id="id" name="id" value="' + item.id + '"/>')
                            } else {
                                $('input[name="id"]', container).val(item.id);
                            }

                            if (item.parent_id == '') {
                                $('input[name="parent_id"]', container).val(widget.todolist.parentId);
                            } else {
                                $('input[name="parent_id"]', container).val(item.parent_id);
                            }
                        });
                    },

                    renderForm: function() {

                        var container = $('#item-info div.modal-body div.card-body', settings.instance);
                        widget.todolist.components = backyard.html.renderForm(
                            'item',
                            container
                        );

                        container.append('<input type="hidden" name="parent_id" value="' + widget.todolist.parentId + '"/>');
                    }
                },
                listener: {

                    /**
                     * 新增資料
                     */
                    add: function() {
                        $('body').off('click', settings.item_add_button_selector);
                        $('body').on('click', settings.item_add_button_selector, function() {
                            widget.todolist.action.renderForm();
                            $('#item-info').modal('toggle');
                        });
                    },

                    /**
                     * 修改資料
                     */
                    modify: function() {
                        $('body').off('click', settings.item_modify_button_selector);
                        $('body').on('click', settings.item_modify_button_selector, function() {
                            var id = $(this).closest('tr.nav-item').attr('id');
                            widget.todolist.action.set(id);
                            $('#item-info').modal('toggle');
                        });
                    },

                    delete: function() {
                        $('body').off('click', settings.item_delete_button_selector);
                        $('body').on('click', settings.item_delete_button_selector, function() {

                            var id = $(this).closest('tr.nav-item').attr('id');

                            backyard.dialog.confirm(
                                '刪除項目',
                                '請注意！確定刪除後項目內的所有資料將<u>無法還原</u>',
                                'warning',
                                function(result) {
                                    if (result.value) {
                                        backyard.data.delete('item', id, function() {
                                            widget.todolist.action.list();
                                        });
                                    }
                                },
                                '刪除',
                                '取消'
                            );
                        });
                    },

                    /**
                     * 按下表單儲存鈕
                     */
                    save: function() {
                        $('body').off('click', settings.item_save_button_selector);
                        $('body').on('click', settings.item_save_button_selector, function() {

                            var container = $('#item-info div.modal-body div.card-body', settings.instance);
                            var extendValue = {};

                            // 取得所有隱藏欄位
                            $('input[type="hidden"]', container).each(function() {
                                extendValue[$(this).attr('name')] = $(this).val();
                            });

                            var id_component = $('input[name="id"]', container);

                            id = (id_component.length > 0) ? id_component.val() : '';
                            if (backyard.data.save(
                                    'item',
                                    id,
                                    widget.todolist.components,
                                    extendValue
                                )) {
                                backyard.dialog.alert('儲存成功', '', 'success');
                                widget.todolist.action.list(widget.todolist.parentId);
                                widget.project.action.list();
                                $('#item-info').modal('toggle');
                            }
                        });
                    },

                }
            },

        }

        widget.project.initial();
        widget.todolist.initial();

        return widget;
    };
}(jQuery));