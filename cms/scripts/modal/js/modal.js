/**
 * Modal
 *
 * @author: Junco CMS (tm)
 * @options:
 * target - (Object) where the modal will be included, can be another modal
 * overlay - (boolean defaults to true) create one overlay div
 * draggable - (boolean defaults to false)
 * destroy - (boolean defaults to true) to destroy or hide the close
 *
 * type - (string defaults to 'default') className of the modal view
 * size - (string defaults to 'medium') className with reference to the size
 * title - (string)
 * content - (string) the html content
 *
 * @events:
 * onLoad
 * onClose
 *
 * @requires
 * - JsElement
 * - JsMergeOptions
 * - JsMove
 * - JsRequest
 * - JsDropdown
 * - JsNotify
 */

var Modal = function (options) {

    // vars
    options = JsMergeOptions({
        target: undefined,
        overlay: true,
        draggable: false,
        destroy: true,
        //
        type: 'default',
        size: 'medium',
        title: '',
        icon: '',
        help_url: null,
        help_title: null,
        content: null,
        buttons: [],
        form: null,
        footer_html: '',

        // events
        //onLoad: null,
        //onClose: null,
    }, options);

    let controls = JsControls({
        modal: {
            close: function () {
                that.close()
            },
            hide: function () {
                that.hide()
            }
        },
    });

    let curFocus;
    let that = {
        isModal: 1,
        close: function () {
            if (element.parentNode) {
                if (options.fire('close', that) === false) {
                    return that;
                }
                toggleFix(false);
                that.blur();

                if (options.destroy) {
                    element.parentNode.removeChild(element);
                } else {
                    that.hide();
                }
                //window.removeEventListener('popstate', pop);
            }
            return false;
        },

        show: function () {
            this.toggle(true);
        },

        hide: function () {
            this.toggle(false);
        },

        toggle: function (status) {
            if (typeof status == 'undefined') {
                status = element.style.display == 'none';
            }
            if (status) {
                this.focus();
            } else {
                this.blur();
            }
            element.style.display = status ? '' : 'none';
        },

        focus: function () {
            if (!getFocusableElements().includes(document.activeElement)) {
                curFocus = document.activeElement;
            }

            options.target?.blur();
            element.querySelector('button[control-modal=close]')?.focus();
            if (options.overlay) {
                element.scroll(0, 0);
            }
            document.addEventListener('keydown', keyboardControls);
        },

        blur: function () {
            options.target?.focus();
            curFocus?.focus();
            document.removeEventListener('keydown', keyboardControls);
        },

        getSubmit: function () {
            return element.querySelector('.modal-footer *[type=submit]');
        },

        getElement: function (selector) {
            if (selector) {
                return _element.querySelector(selector);
            }
            return _element;
        }
    };

    function getFocusableElements() {
        return [
            ..._element.querySelectorAll('a[href],button,input,textarea,select,details,[tabindex]:not([tabindex="-1"])')
        ].filter(
            el => !el.hasAttribute('disabled') && !el.getAttribute('aria-hidden')
        );
    }

    function keyboardControls(event) {
        if (!element) {
            that.blur();
        }

        if (event.key == 'Tab') {
            let focusables = getFocusableElements();
            let length = focusables.length;

            if (!length) {
                event.preventDefault();
            } else if (!focusables.includes(document.activeElement)) {
                event.preventDefault();
                focusables[0].focus();
            } else {
                let isBack = event.shiftKey;
                if (isBack) {
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
            that.close();
        }
    }

    // functions
    function getButtonClass(button) {
        let css = ['btn'];

        if (button.class) {
            css.push(button.class);
        }

        if (button.type == 'submit') {
            css.push('btn-primary btn-solid');
        }

        return css.join(' ');
    }

    function render(number) {
        let header = '';
        let footer = '';

        if (options.title) {
            if (options.icon) {
                header += '<div><i class="' + options.icon + '" aria-hidden="true"></i></div>';
            }
            header += '<div id="modal-' + number + '-title" class="modal-title"><h3>' + options.title + '<h3></div>';

            if (options.help_url) {
                header += '<div><a href="' + options.help_url + '" target="_blank" title="' + options.help_title + '"><i class="fa-solid fa-circle-question"></i></a></div>';
            }
            header = '<div class="modal-header">' + header + '</div>';
        }

        if (options.buttons.length) {
            let x = {
                'help': '',
                'button': '',
                'submit': '',
                'close': ''
            };

            options.buttons.forEach(function (button) {
                x[button.type] += '<button type="' + (button.type == 'submit' ? 'submit' : 'button') + '"'
                    + (button.type != 'submit' ? ' control-modal="' + (button.control || button.type) + '"' : '')
                    + ' class="' + getButtonClass(button) + '">'
                    + button.caption
                    + '</button>';
            });

            footer = '<div class="modal-footer">' + options.footer_html + x.help + x.button + x.submit + x.close + '</div>';
        }

        if (options.form) {
            if (Array.isArray(options.form.hidden)) {
                options.form.hidden.forEach(function (hidden) {
                    footer += '<input type="hidden" name="' + hidden.name + '" value="' + hidden.value + '"/>';
                });
            }

            footer = '<form id="' + options.form.id + '">' + footer + '</form>';
        }

        return '<div class="modal modal-' + options.type + '" tabindex="-1" role="dialog" aria-modal="true" aria-labeledby="modal-' + number + '-title"><div>'
            + header
            + '<div class="modal-body">' + (options.content || '') + '</div>'
            + footer
            + '</div></div>';
    }

    function toggleFix(force) {
        if (options.overlay && element.parentNode == document.body) {
            if (Modal.countFixed == 0 || Modal.countFixed == 1) {
                document.body.classList.toggle('modal-fixed', force);
                document.body.style.width = force ? document.body.clientWidth + 'px' : '';
            }

            Modal.countFixed += (force ? 1 : -1);
        }
    }

    /*function pop(event) {
        console.log(event);
        that.close();
    }

    function backControl() {
        window.addEventListener('popstate', pop);
        window.history.pushState(
            window.history.state,
            window.document.title,
            window.document.location
        );
    }*/

    // ini options
    if (options.type == 'alert') {
        options.size = 'small';
    }

    // element
    var _element = element = JsElement('div.modal-' + options.size, {
        html: render(Modal.countFixed),
        'data-modal': 1,
    });

    // draggable
    if (options.draggable) {
        let handle = element.querySelector('.modal-header');
        let initial;

        function startFn(event) {
            event.stopPropagation();
            event.preventDefault();

            let rect = element.getBoundingClientRect();
            initial = {
                x: rect.left - event.clientX,
                y: rect.top - event.clientY,
            };
        }

        function moveFn(event) {
            element.style.left = (initial.x + event.clientX) + 'px';
            element.style.top = (initial.y + event.clientY) + 'px';
            element.style.margin = '0px'; // hack
        }

        handle.style.cursor = 'move';
        JsMove(handle, startFn, moveFn);

    } else if (options.overlay) { // overlay
        var element = JsElement('div.modal-overlay');

        element.appendChild(_element);
        element.addEventListener('click', that.close);
        _element.addEventListener('click', function (event) {
            event.stopPropagation();
            JsDropdown.hide(); // dropdown
        });
    }

    // load controls
    controls.load('modal', element, function (el, fn) {
        el.addEventListener('click', fn);
    });

    // append
    let parent = document.body;
    if (typeof options.target == 'object') {
        if (options.target.isModal) {
            parent = options.target.getElement();
        } else {
            parent = options.target;
        }
    }
    parent.appendChild(element);

    //
    toggleFix(true);
    that.show();
    options.fire('load', that);
    JsNotify.hide();
    JsNotify.creator(that, element.querySelector('.modal-body'));
    //backControl();
    return that;
};

Modal.countFixed = 0;



/*
 * Implement framework methods
 */
JsRequest.implement({
    modal: function (options) {
        var mo = typeof options.modalOptions == 'object' ? options.modalOptions : null;
        return this.ajax(options, {
            format: 'modal',
            responseType: 'json',
            update: false,
            onSuccess: function (json) {
                Modal(Object.assign({}, mo, json));
            },
        });
    },
});