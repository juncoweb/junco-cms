/**
 * Focus
 * 
 * @param $element
 * @param options
 *  - controls (bool)
 *  - onEscape (?function)
 *  - onError (?function)
 */

function JsFocus($element, options) {
    options = Object.assign({
        controls: true,
        onEscape: null,
        onError: null
    }, options);

    function getFocusables() {
        return Array
            .from($element.querySelectorAll('a[href],button,input,textarea,select,details,[tabindex]:not([tabindex="-1"])'))
            .filter(el =>
                !el.hasAttribute('disabled')
                && !el.getAttribute('aria-hidden')
                && el.getAttribute('type') != 'hidden'
            );
    }

    function fn(event) {
        if (!$element) {
            return options.onError?.call();
        }

        if (event.key == 'Tab') {
            const focusables = getFocusables();
            const length = focusables.length;

            if (!length) {
                event.preventDefault();
            } else if (!focusables.includes(document.activeElement)) {
                event.preventDefault();
                focusables[0].focus();
            } else {
                if (event.shiftKey) {
                    if (document.activeElement == focusables[0]) {
                        event.preventDefault();
                        focusables[length - 1].focus();
                    }
                } else if (document.activeElement == focusables[length - 1]) {
                    event.preventDefault();
                    focusables[0].focus();
                }
            }
        } else if (event.key == 'Escape') {
            options.onEscape?.call();
        }
    }

    //
    let $current = null;

    return {
        add: function () {
            if (!getFocusables().includes(document.activeElement)) {
                $current = document.activeElement;
            }

            if (options.controls) {
                document.addEventListener('keydown', fn);
            }
        },

        remove: function () {
            $current?.focus();

            if (options.controls) {
                document.removeEventListener('keydown', fn);
            }
        },

        toggle: function (status) {
            if (status) {
                this.add();
            } else {
                this.remove();
            }
        }
    };
}