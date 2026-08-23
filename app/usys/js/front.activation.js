
/* --- Usys --- */
var UsysActivation = (function () {
    return {
        reset: function () {
            const $form = document.querySelector('#js-form');
            const _fieldset1 = FormFieldset($form.querySelector('#email_username'));
            const _fieldset2 = _fieldset1.next();
            const $row0 = _fieldset2.getRow(0);
            const $row1 = _fieldset2.getRow(1);

            // events
            $row0.getElement('.btn').addEventListener('click', () => step2(false));
            $row1.getElement('.btn').addEventListener('click', () => step2(true));

            function step2(status) {
                $row1.toggle(!$row0.toggle(status));
                $row1.getElement('input').value = '';
            }

            function step(value) {
                _fieldset1.toggle(!value);
                _fieldset2.toggle(!!value);

                if (value) {
                    $form.option.value = 2;
                    $form.cur_email.value = value;
                }
            }
            step();
            step2(true);

            //
            const _form = JsForm().request({
                url: JsUrl('/usys.activation/send_token'),
                onSuccess: function (res) {
                    if (res.code == 5) {
                        step(res.message);// reset
                    } else if (res.message) {
                        _form.notify(res.message);
                    }
                }
            });
        }
    };
})();

