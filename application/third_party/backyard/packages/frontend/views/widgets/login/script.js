(function($) {

    /**
     * 登入組件
     * 
     * @param {*} _settings 設定值
     */
    $.fn.widget_login = function(_settings) {
        var settings = $.extend({
            'userType': 'admin',
            'code': $(this).attr('widget'),
            'instance': this,
            'login_button_selector': 'button.login'
        }, _settings);

        var metadata = [];

        // 自定義函式
        var widget = {
            form: {
                initial: function() {
                    metadata = $backyard.template.widgets[settings.code];
                    console.log(metadata);
                    widget.form.event.login();
                    widget.form.event.keypress();
                },
                event: {
                    /**
                     * 送出表單
                     */
                    login: function() {
                        $(settings.login_button_selector).click(function() {

                            var data = {
                                'account': $('input[name="account"]').val(),
                                'password': $('input[name="password"]').val()
                            };

                            var url = (metadata.setting.login_event == undefined) ? ('api/login') : metadata.setting.login_event;
                            $backyard.utility.api(
                                url,
                                data,
                                'POST',
                                function(response) {
                                    console.log(response);
                                    if (response.status == 'success') {
                                        location.href = response.url;
                                    } else {
                                        $('p.login-box-msg').html(response.message);
                                        $(settings.instance).addClass('animated shake');
                                        setTimeout(function() {
                                            $(settings.instance).removeClass('animated shake');
                                        }, 1000);
                                    }
                                }
                            );

                        });
                    },

                    keypress: function() {
                        $('input[name="account"]').keypress(function(event) {
                            // Enter
                            if (event.keyCode == 13) {
                                $('input[name="password"]').focus();
                            }
                        });

                        $('input[name="password"]').keypress(function(event) {
                            // Enter
                            if (event.keyCode == 13) {
                                $(settings.login_button_selector).click();
                            }
                        });
                    }
                },
            }
        }

        widget.form.initial();

        return widget;
    };
}(jQuery));