
/* --- Contact --- */
function Contact() {
    const $box = document.getElementById('contact');
    const $form = $box.querySelector('form');
    const _form = JsForm($form, { focusable: false });
    const url = JsUrl('/contact/take');

    function toggle(status) {
        $box.classList.toggle('contact-finish', status);
        $form.reset();
    }

    $box.querySelector('button').addEventListener('click', function () {
        toggle(0);
    });

    _form.request(url, function (res) {
        switch (res.code) {
            //case -1: window.location.reload();
            case 1: toggle(true); return;
            default:
            case 0: _form.notify(res.message); return;
        }
    });
};
