(function($) {

    /**
     * 組件設定元件
     * 
     * @param {*} _settings 設定值
     */
    $.widgetsetting_component = function(_settings) {
        var settings = $.extend({
            'id': '',
            'tip': '',
            'name': '',
            'value': '',
            'class': 'form-control',
            'label': '',
            'source': '',
            'component': $('<div><div class="form-group"><select name="widgetlist"></select></div></div>')
        }, _settings);

        var components = [];
        var value = '';

        // 自定義函式
        var coreMethod = {
            value: '',
            initial: function() {
                settings.component
                    .attr('id', settings.id)
                    .attr('name', settings.name);

                var widgetlist = $('select[name="widgetlist"]', settings.component);
                widgetlist.attr('class', settings.class).val(settings.value);

                $backyard.utility.api(
                    'api/widget/modules', {},
                    'GET',
                    function(response) {
                        for (var key in response.items) {
                            var group = response.items[key].group;
                            var widgets = response.items[key].widgets;

                            var group = $('<optgroup label="' + response.items[key].group + '"></optgroup>');
                            for (var w_key in widgets) {
                                group.append('<option value="' + widgets[w_key].widget + '">' + widgets[w_key].name + '</option>');
                            }
                            widgetlist.append(group);
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
            invalid: function() {
                return $('<invalid for="' + settings.id + '" style="display:none;"></invalid>');
            },
            element: function() {
                return settings.component;
            },
            elementConvertToComponent: function() {
                $('body').on('change', 'div#' + settings.id + ' select[name="widgetlist"]', function() {
                    $('div#' + settings.id + ' > *').not(':first').remove();
                    components = [];
                    $backyard.utility.api(
                        'api/widget/metadata/' + $(this).val(), {},
                        'GET',
                        function(response) {
                            if (response.status != 'success') {
                                return;
                            }

                            // 呈現欄位元件
                            for (var key in response.metadata.fields) {
                                var componentName = response.metadata.fields[key].component + '_component';

                                $backyard.component.load(response.metadata.fields[key].component, function() {
                                    var component = new $[componentName]({
                                        'id': response.metadata.fields[key].frontendVariable,
                                        'name': response.metadata.fields[key].frontendVariable,
                                        'tip': response.metadata.fields[key].fieldTip,
                                        'source': response.metadata.fields[key].source,
                                        'label': response.metadata.fields[key].name
                                    });
                                    component.initial();

                                    var fieldContainer = $('<div class="form-group"></div>');
                                    fieldContainer.append(component.label());
                                    fieldContainer.append(component.tip());
                                    fieldContainer.append(component.invalid());
                                    fieldContainer.append('<br />');
                                    fieldContainer.append(component.element());

                                    settings.component.append(fieldContainer);
                                    component.elementConvertToComponent();

                                    component.setValue('');
                                    components[response.metadata.fields[key].frontendVariable] = component;
                                });


                            }

                            // 呈現事件欄位
                            for (var key in response.metadata.events) {

                                var componentName = response.metadata.events[key].component + '_component';


                                $backyard.component.load(response.metadata.events[key].component, function() {
                                    var component = new $[componentName]({
                                        'id': response.metadata.events[key].frontendVariable,
                                        'name': response.metadata.events[key].frontendVariable,
                                        'tip': response.metadata.events[key].fieldTip,
                                        'source': response.metadata.events[key].source,
                                        'label': response.metadata.events[key].name
                                    });
                                    component.initial();

                                    var fieldContainer = $('<div class="form-group"></div>');
                                    fieldContainer.append(component.label());
                                    fieldContainer.append(component.tip());
                                    fieldContainer.append(component.invalid());
                                    fieldContainer.append('<br />');
                                    fieldContainer.append(component.element());

                                    settings.component.append(fieldContainer);
                                    component.elementConvertToComponent();

                                    component.setValue('');
                                    components[response.metadata.events[key].frontendVariable] = component;
                                });

                            }
                        },
                        null,
                        'JSON',
                        false
                    );
                });
            },
            getName: function() {
                return settings.name;
            },
            getValue: function() {
                var values = {};
                values['code'] = $('select[name="widgetlist"]', settings.component).val();
                for (var key in components) {
                    values[key] = components[key].getValue();
                }
                return values;
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
                    return;
                }

                $('select[name="widgetlist"]', settings.component).val(value.code);
                $('select[name="widgetlist"]', settings.component).change();
                for (var key in components) {
                    value[key] = (value[key] == undefined) ? '' : value[key];
                    components[key].setValue(value[key]);
                }
            },
            showInList: function(value) {
                return value;
            }

        };

        return coreMethod;
    };
}(jQuery));