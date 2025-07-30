
/* --- Usys ------------------------------------------------------ */
function UsysAccount() {
    var $form = JsForm().request(JsUrl('/usys.account/update'), function (res) {
        $form.notify(res.message);
    });
}