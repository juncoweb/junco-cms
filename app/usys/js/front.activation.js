
/* --- Usys ------------------------------------------------------ */
var UsysActivation = (function () {
    function toggle(value) {
        var f = document.querySelector('#js-form');
        var step = document.querySelectorAll('#js-form > div');
        step[0].style.display = value ? 'none' : '';
        step[1].style.display = !value ? 'none' : '';

        if (value) {
            f.option.value = 2;
            f.cur_email.value = value;
        } else {
            var group = step[1].querySelectorAll('.form-group');
            group[1].style.display = 'none';
            function fn() {
                var force = group[0].style.display == 'none';
                group[0].style.display = force ? '' : 'none';
                group[1].style.display = force ? 'none' : '';
                group[1].querySelector('input').value = '';
            }

            // set event
            group[0].querySelector('.btn').addEventListener('click', fn);
            group[1].querySelector('.btn').addEventListener('click', fn);
        }
    }

    function form(route) {
        var $form = JsForm().request({
            url: JsUrl(route),
            onSuccess: function (message, code) {
                if (code == 5) {
                    toggle(message);// reset
                } else if (message) {
                    $form.notify(message);
                }
            }
        });
    }

    return {
        reset: function () {
            toggle();
            form('/usys.activation/send_token');
        }
    };
})();

