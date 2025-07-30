
/* --- Usys ------------------------------------------------------ */
var UsysPassword = (function () {
    function form(route) {
        var $form = JsForm().request({
            url: JsUrl(route),
            onSuccess: function (res) {
                if (res.message) {
                    $form.notify(res.message);
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

