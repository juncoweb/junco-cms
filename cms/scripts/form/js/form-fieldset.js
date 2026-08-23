/* --- FormFieldset --- */
const FormFieldset = function (el) {
    if (typeof el === 'string') {
        el = document.querySelector(el);
    }

    if (typeof el !== 'object') {
        return null;
    }

    function getFieldset(el) {
        while (el.tagName !== 'BODY') {
            if (el.classList.contains('form-fieldset')) {
                return el;
            }
            el = el.parentNode;
        }
    }

    function getSibling(el, jump) {
        const total = Math.abs(jump);
        const forward = jump > 0;

        for (let i = 0; i < total; i++) {
            if (forward) {
                el = el.nextElementSibling;
            } else {
                el = el.previousElementSibling;
            }

            if (!el) {
                return null;
            }
        }

        return FormFieldset(el);
    }

    const $fieldset = getFieldset(el);

    if (typeof $fieldset !== 'object') {
        return null;
    }

    let that = {
        getRow: function (number = 0) {
            const rows = $fieldset.querySelectorAll('.form-body .form-group');
            if (rows.length > number) {
                return FormRow(rows[number]);
            }
            return null;
        },

        getElement: function (selector) {
            if (selector) {
                return $fieldset.querySelector(selector);
            }
            return $fieldset;
        },

        prev: function (jump = 1) {
            return getSibling($fieldset, -jump);
        },

        next: function (jump = 1) {
            return getSibling($fieldset, jump);
        },

        toggle: function (status) {
            if (typeof status == 'undefined') {
                status = $fieldset.style.display == 'none';
            }

            $fieldset.style.display = status ? '' : 'none';
            return that;
        },

        remove: function () {
            $fieldset.parentNode.removeChild($fieldset);
        },
    };

    return that;
}
