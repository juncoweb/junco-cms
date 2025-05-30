
/* --- Email ----------------------------------------- */
var Email = (function () {
    function $U(task) {
        return JsUrl('admin/email/' + task);
    }

    return {
        write: function () {
            var _FormBox = FormBox()
                .tab({
                    url: $U('form'),
                    onLoad: function () {
                        var $form = JsForm();
                        $form.request($U('send'), function (message, code) {
                            if (code) {
                                $form.reset();
                            }
                            _FormBox.notify(message);
                        });
                    },
                })
                .select();
        },
        debug: function () {
            let t = JsTabs('#tabs');
            t.select();

            JsForm().request({
                url: $U('take'),
                load: 'json'
            }, function (json) {
                document.querySelector('#email-code').innerHTML = json.code;
                document.querySelector('#email-debug').innerHTML = json.debug;
                t.select(1);
            });

        }
    };
})();
