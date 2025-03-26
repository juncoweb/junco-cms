/**
 * Javascript Form Handler
 *
 * @abstract
 * Plugable form control. Prepare the form to be sent through different methods.
 *
 * @params:
 * form - (string or object) the form implement
 * options - (object, optional)
 *
 *
 */
var JsForm = function (form, options = {}) {
    if (typeof form == 'object' && form.tagName != 'FORM') {
        options = form;
        form = '';
    }
    if (!form || typeof form == 'number') {
        form = document.querySelector('#js-form' + (form || ''));
    } else if (typeof form == 'string') {
        try {
            form = document.querySelector(form) || document.querySelector('#' + form + '-form');
        } catch (e) { }
    }

    if (!form || form.tagName != 'FORM') {
        return;
    }

    options = Object.assign({ focusable: true }, options);
    var btn = options.btn;



    function focus() {
        function fn(types, checkables = [], tags = []) {
            for (let el, i = 0; el = form.elements[i]; i++) {
                if (el.name && !el.disabled) {
                    if (
                        tags.includes(el.tagName)
                        || types.includes(el.type)
                        || (checkables.includes(el.type) && el.checked)
                    ) {

                        return el;
                    }
                }
            }
        }

        let el = fn(['text', 'number', 'password', 'url', 'email', 'date'], ['checkbox', 'radio'], ['SELECT', 'TEXTAREA'])
            || fn(['submit'])
            || fn(['button']);

        if (el) {
            el.focus();
        }
    }

    // vars
    var $U;
    var _data = [];
    var _controls = JsControls({
        form: {}
    });
    var that = {
        url: function (fn) {
            if (typeof fn == 'function') {
                $U = fn;
            }
            return this;
        },

        controls: function (options) {
            for (var ctrlName in options) {
                switch (typeof options[ctrlName]) {
                    case 'object': // create function
                        options[ctrlName] = (function (options, ctrlName) {
                            // load
                            var load = options.load || 'xjs';

                            if (typeof options.modalOptions == 'object' && load != 'modal') {
                                load = 'modal';
                            }

                            if (!options.url && $U) {
                                options.url = $U(ctrlName);
                            }

                            return function (el) {
                                if (typeof options.onSubmit == 'function'
                                    && !options.onSubmit(el)
                                ) {
                                    return;
                                }

                                // data
                                var data = [];
                                var value = el.getAttribute('data-value');

                                if (typeof value != 'undefined') {
                                    data.push({
                                        [ctrlName]: value
                                    });
                                }

                                switch (typeof options.data) {
                                    case 'function':
                                    case 'string':
                                    case 'object':
                                        data.push(options.data);
                                        break;
                                }

                                // execute
                                JsRequest[load](Object.assign({}, options, {
                                    data: data
                                }));
                            };
                        })(options[ctrlName], ctrlName);

                    case 'function':
                        _controls.attach('form', ctrlName, options[ctrlName]);
                        break;
                }
            }

            _controls.load('form', form, function (el, fn) {
                var eventName = 'click';
                if (el.nodeName == 'SELECT' || (el.type && ['checkbox', 'radio'].indexOf(el.type) != -1)) {
                    eventName = 'change';
                } else if (el.nodeName == 'INPUT') {
                    eventName = 'blur';
                }

                el.addEventListener(eventName, function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    document.body.click(); // Throw event for dropdown or similar
                    fn(el);
                });
            });

            return this;
        },

        /*
         * request
         *
         * @options (function or object or string)
         * - if function, execute!
         *   this is the model:
         *
         *   JsRequest.xjs({
         *     url: JsUrl(),
         *     data: this.getData(),
         *     spinner: {disable:this.getSubmit()},
         *   );
         *
         * - if object,
         *   add
         *    options.data
         *    options.spinner
         *
         *   and execute
         *     JsRequest[(load || 'xjs')](options);
         *
         * - if string,
         *   create the object options:
         *   options = {url: options}
         *
         *   and execute!
         *
         * @load (string) - optional - change the request load in object or string options.
         *
         */
        request: function (options, callback, load) {
            switch (typeof options) {
                case 'function':
                    this.submit = options;
                    break;
                case 'string':
                    options = {
                        url: options
                    };
                // break;
                case 'object':
                    var options_data = options.data;
                    var isArray = Array.isArray(options_data);

                    if (typeof options.modalOptions == 'object') {
                        load = 'modal';
                    } else if (typeof options.load != 'undefined' && typeof options.load) {
                        load = options.load;
                    } else if (!load) {
                        load = 'xjs';
                    }

                    // callback
                    if (typeof callback == 'function') {
                        options.onSuccess = callback;
                    }

                    // reset
                    if (typeof options.onReset == 'function') {
                        form.addEventListener('reset', function (event) {
                            options.onReset.call(form, event);
                        });
                    }

                    this.submit = function () {
                        // onSubmit
                        if (typeof options.onSubmit == 'function' && !options.onSubmit(form)) {
                            return;
                        }

                        // spinner
                        if (typeof options.spinner == 'undefined') {
                            options.spinner = {
                                disable: this.getSubmit()
                            };
                        }

                        // data
                        var data = this.getData();

                        if (options_data) {
                            if (!Array.isArray(data)) {
                                data = [data];
                            }
                            if (isArray) {
                                data.concat(options_data);
                            } else {
                                data.push(options_data);
                            }
                        }

                        options.data = data;

                        // Request
                        JsRequest[load](options);
                    };
                    break;
            }

            return this;
        },

        /*
         * Helper functions
         */
        getData: function () {
            return _data.length ? [form, _data] : form;
        },

        getForm: function (name) {
            if (name) {
                return typeof form[name] != 'undefined' ? form[name].value : undefined;
            }
            return form;
        },

        getSubmit: function () {
            if (!btn) {
                btn = form.querySelector('*[type=submit]');
            }

            return btn;
        },

        reset: function () {
            form.reset();
        },

        notify: function (options) {
            JsNotify.creator(form).notify(options);
        },
    };


    // submit button
    switch (typeof btn) {
        case 'object':
            if (btn.isModal) {
                btn = btn.getSubmit();
            }
            break;

        case 'string':
            btn = document.querySelector(btn);
            break;
    }

    if (btn) {
        btn.setAttribute('form', form);
        if (!btn.form) { // not html5 support
            btn.addEventListener('click', function () {
                that.submit();
            });
        }
    }

    JsFelem.load(form, _data);
    JsControls({
        form: {
            'toggle-body': function (el) {
                for (var box = el; !box.classList.contains('form-fieldset'); box = box.parentNode) {
                    if (box.tagName == 'BODY') {
                        return;
                    }
                }

                el.addEventListener('click', function () {
                    el.setAttribute('aria-expanded', box.classList.toggle('expanded'));
                });
            },
        }
    }).load('form', form);

    // event
    form.addEventListener('submit', function (event) {
        event.preventDefault();
        that.submit();
    });

    if (options.focusable) {
        focus();
    }

    return that;
};