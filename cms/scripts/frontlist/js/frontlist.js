/* --- Frontlist ---------------------------------------------- */
var Frontlist = function (ID) {
    var box = document.getElementById((ID ? ID + '-' : '') + 'frontlist');
    if (!box) {
        return;
    }

    // vars
    var $U;
    var _controls = JsControls({
        list: {
            refresh: function () {
                that.refresh();
            },
        },
    });

    // initialize
    var _options = {};
    var _initialize = function (options) {
        switch (typeof options) {
            case 'string':
                _options.url = options;
                break;
            case 'object':
                _options = options;
                break;
        }

        if (!_options.url) {
            if ($U) {
                _options.url = $U('list');
            } else if (allowHistory) {
                _options.url = document.location.href;
            } else {
                alert('The url is undefined.');
            }
        }

        _options.format = 'text';
        if (typeof _options.onSuccess == 'function') {
            _onSuccess = _options.onSuccess;
        }
        _options.onSuccess = function (html) {
            lbox.innerHTML = html;
            LoadList();
        };
    };

    // history
    var _history;
    var allowHistory = true;
    function LoadHistory() {
        if (allowHistory && JsHistory.check()) {
            _history = JsHistory('Frontlist', function (event) {
                _options.url = document.location.href;
                that.goTo();
            });
        }
    }

    // options
    function LoadActions() {
        let obox = box.querySelector('div[frontlist-actions]');
        if (obox) {
            _controls.load('list', obox, function (el, fn, i) {
                el.addEventListener('click', function (event) {
                    event.preventDefault();
                    JsDropdown.hide();
                    fn(el);
                });
            });
            JsFelem.load(obox);
        }
    }

    // list
    var lbox = box.querySelector('div[frontlist-slot]');
    var _onSuccess;
    function LoadList() {
        _controls.load('list', lbox, function (el, fn) {
            el.addEventListener('click', function (event) {
                event.preventDefault();
                fn(el);
            });
        });
        JsFelem.load(lbox);

        if (_onSuccess) {
            _onSuccess(lbox);
        }

        LoadListPagination();
        LoadListFilters();
    }

    // pagination
    function LoadListPagination() {
        box.querySelectorAll('*[control-page]').forEach(function (el) {
            el.addEventListener('click', function (event) {
                event.preventDefault();
                that.goTo(el.href);
            });
        });
    }

    // filters
    function LoadListFilters() {
        let form = box.querySelector('#list-filters form');
        if (form) {
            form.addEventListener('submit', (function () {
                return function (event) {
                    event.preventDefault();
                    let url = _options.url.split('?')[0] + '?' + toQueryString(form);
                    that.goTo(url);
                }
            })());
            //JsFelem.load(form);
        }
    }

    // functions
    function toQueryString(f) {
        var _data = [],
            el, i = 0;
        for (; el = f.elements[i]; i++) {
            if (el.name && !el.disabled) {
                if (el.type == 'select-multiple') {
                    for (var j = 0, m = el.options.length; j < m; j++) {
                        if (el.options[j].selected) {
                            _data.push(el.name + '=' + encodeURIComponent(el.options[j].value));
                        }
                    }
                } else if (['checkbox', 'radio'].indexOf(el.type) == -1 || el.checked) {
                    _data.push(el.name + '=' + encodeURIComponent(el.value));
                }
            }
        }
        return _data.join('&');
    }

    function objToFn(options, cmd) {
        options = Object.assign({}, options);
        if (typeof options.modalOptions == 'object') {
            options.load = 'modal';
        } else if (typeof options.load == 'undefined') {
            options.load = 'xjs';
        }

        if (!options.url && $U) {
            options.url = $U(cmd);
        }

        return function (el) {
            options.getTarget = function () {
                return el;
            };
            options.getId = function () {
                return that.getId(el);
            };
            options.getRow = function () {
                return that.getRow(el);
            };

            if (typeof options.onSubmit == 'function' && !options.onSubmit()) {
                return;
            }

            // data
            let data = [];
            let id = that.getId(el);
            if (id) {
                data.push({
                    id: id
                });
            }
            let value = el.getAttribute('data-value');
            if (value) {
                data.push({
                    [el.getAttribute('data-name') || cmd]: value
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
            JsRequest.load(Object.assign({}, options, {
                data: data
            }));
        };
    }

    // That
    var that = {
        url: function (fn) {
            if (typeof fn == 'function') {
                $U = fn;
            }
            return this;
        },

        controls: function (options, handler) {
            handler = handler || 'list';

            for (let cmd in options) {
                switch (typeof options[cmd]) {
                    case 'object':
                        options[cmd] = objToFn(options[cmd], cmd);
                    case 'function':
                        _controls.attach(handler, cmd, options[cmd]);
                }
            }

            return this;
        },

        allowHistory: function (status = true) {
            allowHistory = status;

            return this;
        },

        load: function (options) {
            if (_initialize) {
                _initialize = _initialize(options);
                LoadHistory();
                LoadActions();
            }

            if (!lbox.innerHTML) {
                this.goTo(_options.url);
            } else {
                LoadList();
            }

            return this;
        },

        goTo: function (url) {
            if (url) {
                _options.url = url;

                if (allowHistory) {
                    _history.push(document.title, url);
                }
            }

            _options.data = {
                'async': 1
            };

            JsRequest.load(_options);
        },

        refresh: function () {
            this.goTo();
        },

        notify: function (options) {
            box.notify(options);
        },

        getId: function (el) {
            for (; el.tagName != 'BODY'; el = el.parentNode) {
                let value = el.getAttribute('control-row');
                if (value !== null) {
                    return value;
                }
            }
        },

        getRow: function (el) {
            for (; el.tagName != 'BODY'; el = el.parentNode) {
                if (el.getAttribute('control-row') !== null) {
                    return el;
                }
            }
        }
    };

    JsNotify.creator(box);

    return that;
};