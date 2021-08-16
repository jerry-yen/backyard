(function($) {

    /**
     * HTML編輯器元件
     * 
     * @param {*} _settings 設定值
     */
    $.htmleditor_component = function(_settings) {
        var settings = $.extend({
            'id': '',
            'tip': '',
            'name': '',
            'value': '',
            'class': 'form-control',
            'label': '',
            'source': '',
            'component': $('<textarea></textarea>')
        }, _settings);

        // 自定義函式
        var coreMethod = {
            initial: function() {
                settings.component
                    .attr('id', settings.id)
                    .attr('class', settings.class)
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
                // 因為容易有時間差的關係，導致編輯器套件沒有載入
                // 所以故意延遲1秒再載入
                setTimeout(function() {
                    tinymce.init({
                        selector: 'textarea#' + settings.id,
                        height: 500,
                        menubar: false,
                        plugins: [
                            'advlist autolink lists link image charmap print preview anchor',
                            'searchreplace visualblocks code fullscreen',
                            'insertdatetime media table paste code help wordcount responsivefilemanager '
                        ],
                        relative_urls: false,
                        remove_script_host: false,
                        image_advtab: true,
                        toolbar: 'undo redo | formatselect | ' +
                            'bold italic backcolor | alignleft aligncenter ' +
                            'alignright alignjustify | bullist numlist outdent indent | ' +
                            'removeformat | responsivefilemanager insertfile image media pageembed template link anchor codesample | code',
                        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
                        language: 'zh_TW',
                        file_picker_types: 'file image media',
                        external_filemanager_path: "{adminlte}/plugins/tinymce-5.7.0/plugins/filemanager/",
                        filemanager_title: "媒體中心",
                        external_plugins: {
                            "responsivefilemanager": "./plugins/responsivefilemanager/plugin.min.js",
                            "filemanager": "./plugins/filemanager/plugin.min.js"
                        },
                        /* and here's our custom image picker*/
                        activate: function(api) {
                            console.log(api);
                            alert('test');
                        }
                    });
                }, 1000);


            },
            getName: function() {
                return settings.name;
            },
            getValue: function() {
                return tinymce.activeEditor.getContent();
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
                setTimeout(function() {
                    tinymce.activeEditor.setContent(value);
                }, 500);
            },
            showInList: function(value) {
                return value;
            }
        };

        return coreMethod;
    };
}(jQuery));