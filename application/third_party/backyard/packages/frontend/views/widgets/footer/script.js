(function($) {

    /**
     * 頁底組件
     * 
     * @param {*} _settings 設定值
     */
    $.fn.widget_footer = function(_settings) {
        var settings = $.extend({
            'userType': 'admin',
            'code': $(this).attr('widget'),
        }, _settings);



        // 自定義函式
        var widget = {

        }

        return widget;
    };
}(jQuery));