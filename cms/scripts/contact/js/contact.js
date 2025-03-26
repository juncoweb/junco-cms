
/* --- Contact ---------------------------------------- */
function Contact() {
    let $box = document.getElementById('contact');
    let $form = $box.querySelector('form');
    let _form = JsForm($form, { focusable: false });
    let url = JsUrl('/contact/take');

    function toggle(status) {
        $box.classList.toggle('contact-finish', status);
        $form.reset();
    }

    $box.querySelector('button').addEventListener('click', function () {
        toggle(0);
    });

    _form.request(url, function (message, code) {
        switch (code) {
            //case -1: window.location.reload();
            case 1: toggle(true); return;
            default:
            case 0: _form.notify(message); return;
        }
    });
};
