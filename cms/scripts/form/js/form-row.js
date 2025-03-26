/* --- FormRow ----------------------------- */
const FormRow = function (el) {
    if (typeof el === 'string') {
        el = document.querySelector(el);
    }

    if (typeof el !== 'object') {
        return null;
    }

    function getRow(el) {
        while (el.tagName !== 'BODY') {
            if (el.classList.contains('form-group')) {
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

        return FormRow(el);
    }

    const $row = getRow(el);

    if (typeof $row !== 'object') {
        return null;
    }

    let that = {
        prev: function (jump = 1) {
            return getSibling($row, -jump);
        },

        next: function (jump = 1) {
            return getSibling($row, jump);
        },

        toggle: function (status) {
            $row.style.display = status ? '' : 'none';
            return that;
        },

        clone: function (callback) {
            let $new = $row.cloneNode(true);

            if (typeof callback === 'function') {
                $new = callback($new);
            }
            return FormRow($new);
        },

        getElement: function (selector) {
            if (selector) {
                return $row.querySelector(selector);
            }
            return $row;
        },

        insertBefore: function ($new) {
            $row.parentNode.insertBefore($new.getElement(), $row);
            return $new;
        },

        insertAfter: function ($new) {
            const el = $row.nextElementSibling;

            if (el) {
                el.parentNode.insertBefore($new.getElement(), el);
            } else {
                $row.parentNode.appendChild($new.getElement());
            }

            return $new;
        },

        remove: function () {
            $row.parentNode.removeChild($row);
        }
    };

    return that;
}

/* --- FormFieldset ----------------------------- */
const FormFieldset = function (el) {
    if (typeof el === 'string') {
        el = document.querySelector(el);
    }

    if (typeof el !== 'object') {
        return null;
    }

    function getRow(el) {
        while (el.tagName !== 'BODY') {
            if (el.classList.contains('form-fieldset')) {
                return el;
            }
            el = el.parentNode;
        }
    }

    const $fieldset = getRow(el);

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

        remove: function () {
            $fieldset.parentNode.removeChild($fieldset);
        }
    };

    return that;
}
