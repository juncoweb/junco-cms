/* --- FormCol --- */
const FormCol = function (el) {
    function getCol(el) {
        while (el.tagName !== 'BODY') {
            if (
                el.classList.contains('form-group')
                && el.parentNode.parentNode.classList.contains('form-columns')) {
                return el.parentNode;
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

        returCol(el);
    }

    //
    if (typeof el === 'string') {
        el = document.querySelector(el);
    }

    if (typeof el !== 'object') {
        return null;
    }

    const $col = getCol(el);

    if (typeof $col !== 'object') {
        return null;
    }

    const that = {
        prev: function (jump = 1) {
            return getSibling($col, -jump);
        },

        next: function (jump = 1) {
            return getSibling($col, jump);
        },

        toggle: function (status) {
            if (typeof status == 'undefined') {
                status = $col.style.display == 'none';
            }
            $col.style.display = status ? '' : 'none';
            return status;
        },

        clone: function (callback) {
            let $new = $col.cloneNode(true);

            if (typeof callback === 'function') {
                $new = callback($new);
            }
            return FormCol($new);
        },

        getElement: function (selector) {
            if (selector) {
                return $col.querySelector(selector);
            }
            return $col;
        },

        insertBefore: function ($new) {
            $col.parentNode.insertBefore($new.getElement(), $col);
            return $new;
        },

        insertAfter: function ($new) {
            const el = $col.nextElementSibling;

            if (el) {
                el.parentNode.insertBefore($new.getElement(), el);
            } else {
                $col.parentNode.appendChild($new.getElement());
            }

            return $new;
        },

        remove: function () {
            $col.parentNode.removeChild($col);
        }
    };

    return that;
}