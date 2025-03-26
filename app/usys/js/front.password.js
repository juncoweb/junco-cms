
/* --- Usys ------------------------------------------------------ */
var UsysPassword = (function () {
    function form(route) {
        var $form = JsForm().request({
            url: JsUrl(route),
            onSuccess: function (message, code) {
                if (message) {
                    $form.notify(message);
                }
            },
        });
    }

    return {
        reset: function () {
            form('/usys.password/send_token');
        },
        edit: function () {
            form('/usys.password/update');
        }
    };
})();

