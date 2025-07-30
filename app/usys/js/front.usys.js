
/* --- Usys ------------------------------------------------------ */
var UsysLock = (function () {
    var btnText;
    return function ($form, expires) {
        var date = new Date(expires * 1000 - Date.now());
        var value = parseInt(date.getTime() / 1000);
        var btn = (function () {
            var btn = $form.getSubmit();
            var attr = btn.tagName == 'BUTTON' ? 'innerHTML' : 'value';

            return {
                get: function () {
                    return btn[attr];
                },
                set: function (value) {
                    btn[attr] = value;
                },
                status: function (value) {
                    btn.disabled = value;
                }
            }
        })();

        if (!btnText) {
            btnText = btn.get();
        }

        if (value > 0) {
            var handle = setInterval(function () {
                if (value) {
                    btn.set(btnText + ' (' + value + ')');
                    value--;
                } else {
                    btn.set(btnText);
                    btn.status(false);
                    clearInterval(handle);
                }
            }, 1000);
            btn.status(true);
        }
    };
})();

var Usys = (function () {
    function form(route) {
        var $form = JsForm().request({
            url: JsUrl(route),
            onSuccess: function (res) {
                if (res.data && res.data.lockExpires > 0) {
                    UsysLock($form, res.data.lockExpires);
                }
                if (res.message) {
                    $form.notify(res.message);
                }
            },
        });
    }

    return {
        load: function () {
            form('/usys/take_login');
        },

        signup: function () {
            form('/usys/take_signup');
        },

        login: function () {
            JsRequest.modal({
                url: JsUrl('/usys/login'),
                modalOptions: {
                    size: 'medium',
                    onLoad: function () {
                        Usys.load();
                    },
                }
            });
        },
    };
})();
