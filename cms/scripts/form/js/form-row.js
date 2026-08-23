/* --- FormRow --- */
const FormRow = function (el) {
    if (typeof el === 'string') {
        el = document.querySelector(el);
    }

    if (typeof el !== 'object') {
        return null;
    }

    function isRow(el) {
        return el.classList.contains('form-group');
    }

    function getRow(el) {
        while (el.tagName !== 'BODY') {
            if (isRow(el)) {
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
            if (typeof status == 'undefined') {
                status = $row.style.display == 'none';
            }
            $row.style.display = status ? '' : 'none';
            return status;
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