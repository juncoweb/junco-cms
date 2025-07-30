
/* --- Language --------------------------------- */
window.addEventListener('load', function () {
    let f = JsForm('#language-form').request(JsUrl('/language/change'), function (res) {
        if (res.ok()) {
            window.location.reload();
        } else {
            alert(res.message);
        }
    });

    f.getForm().lang.addEventListener('change', function () {
        f.submit();
    });
});