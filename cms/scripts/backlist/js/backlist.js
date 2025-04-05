/* --- Backlist ---------------- */
function Backlist(ID) {
    /**
     * Manages the rows in the list.
     * 
     * @param {JsControls} _controls
     * @returns {Object}
     */
    function Rows(_controls) {
        let rows = [];
        let that = {
            getButtons: function () {
                return _buttons;
            },

            getCheckAll: function () {
                return _checkall;
            },

            add: function (row) {
                let checkbox = row.querySelector('input[type=checkbox]');

                if (!checkbox || checkbox.disabled) {
                    return;
                }

                let data = row.getAttribute('row-labels');
                let labels = data ? data.split(',') : [];
                function toggle(state) {
                    checkbox.checked = state;
                    row.classList.toggle('active', state);
                    _buttons.refresh();
                    refresh();
                }

                checkbox.addEventListener('change', function (event) {
                    event.stopPropagation();
                    toggle(!checkbox.checked);
                });

                row.addEventListener('click', function () {
                    toggle(!checkbox.checked);
                });

                rows.push({
                    checkbox,
                    row,
                    toggle,
                    hasLabel: function (label) {
                        return labels.indexOf(label) != -1;
                    }
                });
            },

            restart: function () {
                rows = [];
            },

            numSelected: function () {
                let count = 0;
                let i = rows.length;

                while (i-- && count < 2) {
                    if (rows[i].checkbox.checked) {
                        count++;
                    }
                }

                return count;
            },

            hasLabel: function (label) {
                let i = rows.length;

                while (i--) {
                    if (
                        rows[i].checkbox.checked
                        && !rows[i].hasLabel(label)
                    ) {
                        return false;
                    }
                }

                return true;
            },

            hasLabelFrom: function (btn, label) {
                let row = getRowFromButton(btn);
                let i = rows.length;

                while (i--) {
                    if (rows[i].row == row) {
                        return rows[i].hasLabel(label);
                    }
                }

                return true;
            },

            select: function (btn) {
                let i = rows.length;
                let row = getRowFromButton(btn);

                while (i--) {
                    rows[i].toggle(rows[i].row == row);
                }
            }
        };

        let _buttons = Buttons(that);
        let _checkall = CheckAll(function (status) {
            let i = rows.length;

            while (i--) {
                rows[i].toggle(status);
            }

            _buttons.refresh();
        });

        function refresh() {
            let i = rows.length;

            while (i--) {
                if (!rows[i].checkbox.checked) {
                    return _checkall.checked(false);
                }
            }

            return _checkall.checked(true);
        }

        function getRowFromButton(btn) {
            let row = btn;

            for (; row.getAttribute('control-row') == undefined; row = row.parentNode) {
                if (row.tagName == 'BODY') {
                    return null;
                }
            }

            return row;
        }

        _controls.attachAll('row', {
            default: function (el) {
                that.add(el);
            },

            'check-all': function (el) {
                _checkall.add(el);
            },
        });

        return that;
    }

    /**
     * Manage the buttons of the options and rows.
     * 
     * @param {Rows} _row 
     * @returns {Object}
     */
    function Buttons(_row) {
        let buttons = [];
        let rules = {};
        let labels = {};
        let values = [
            { '0': true, '1': false, '+': false, '?': true },
            { '0': false, '1': true, '+': true, '?': true },
            { '0': false, '1': false, '+': true, '?': false }
        ];

        function getButton(target) {
            return buttons.find((button) => button.target == target);
        }

        let that = {
            add: function (name, target) {
                if (['0', '1', '+', '?'].indexOf(rules[name]) !== -1) {
                    buttons.push({
                        target: target,
                        status: function (total) {
                            let status = values[total][rules[name]];

                            if (status && labels[name]) {
                                return _row.hasLabel(labels[name]);
                            }

                            return status;
                        },
                        virtuallyStatus: function () {
                            let status = values['1'][rules[name]];

                            if (status && labels[name]) {
                                return _row.hasLabelFrom(target, labels[name]);
                            }

                            return status;
                        }
                    });
                }
            },

            /**
             * Add a command.
             * 
             * @param {String} name
             * @param {String} numRows  Options:
             *  0 = 0
             *  1 = 1
             *  + = 1 or more
             *  ? = 0 or 1
             *  * = 0 or more. Not attach
             */
            addCmd: function (name, numRows, onlyRows) {
                rules[name] = String(numRows);

                if (typeof onlyRows !== 'undefined') {
                    if (onlyRows === true) {
                        onlyRows = name;
                    }

                    labels[name] = String(onlyRows);
                }
            },

            refresh: function () {
                let total = _row.numSelected();

                buttons.forEach(function (button) {
                    let disabled = !button.status(total);

                    button.target.classList.toggle('disabled', disabled);
                    button.target.setAttribute('aria-disabled', disabled);
                });
            },

            clean: function () {
                buttons = buttons.filter(function (button) {
                    return document.body.contains(button.target);
                });
            },

            isDisabled: function (el) {
                return el.classList.contains('disabled') || el.getAttribute('aria-disabled') === 'true';
            },

            isVirtuallyDisabled: function (el) {
                let button = getButton(el);

                return button ? !button.virtuallyStatus() : false;
            }
        };

        return that;
    }

    /**
     * Manages the "check/uncheck all" elements.
     * 
     * @param {Function} callback 
     * @returns {Object}
     */
    function CheckAll(callback) {
        let elements = [];
        let that = {
            toggle: function (force) {
                callback(force);
            },

            check: function () {
                this.toggle(true);
            },

            uncheck: function () {
                this.toggle(false);
            },

            checked: function (force) {
                elements.forEach(function (el) {
                    el.toggle(force);
                });
            },

            add: function (el) {
                let cmd = el.getAttribute('data-checkall');

                if (!['uncheck', 'check'].includes(cmd)) {
                    cmd = 'toggle';
                }

                if (el.tagName == 'INPUT') {
                    el.addEventListener('change', function (event) {
                        event.stopPropagation();
                        that[cmd](el.checked);
                    });

                    el.toggle = function (force) {
                        el.checked = force;
                    }

                } else {
                    el.addEventListener('click', function () {
                        that[cmd](el.getAttribute('data-toggle'));
                    });

                    el.toggle = function (force) {
                        el.setAttribute('data-toggle', force);
                    }
                }

                elements.push(el);
            },
        };

        return that;
    }

    /**
     * Manage the filters.
     * 
     * @param {JsControls} _controls 
     * @param {Function} callback 
     * @returns {Object} 
     */
    function Filters(_controls, callback) {
        let $box, $filters, $form;

        function Sticky(el) {
            if (typeof el == 'string') {
                el = document.querySelector(el);
            }
            let v = 0;
            let H = document.querySelector('header[data-sticky]');

            function fn() {
                let top = H ? H.getBoundingClientRect().height : 0;

                if (v != (document.documentElement.scrollTop > top)) {
                    v = el.classList.toggle('active');
                    el.style.top = top + 'px';
                    TooltipActive.hide();
                }
            }

            fn();
            window.addEventListener('scroll', fn);
        }

        let that = {
            toggle: function () {
                let value = $filters.style.display == '' ? 'none' : '';

                $filters.style.display = value;
                JsCookie.set('ListFilters', value);
            },

            reset: function () {
                callback();
            },

            setBox: function (el) {
                el = el.querySelector('[backlist-actions]');
                $box = el.firstChild.nextSibling;

                Sticky(el);
            },

            load: function (el) {
                $filters = el.querySelector('[backlist-filters]');
                if (!$filters) {
                    return;
                }

                _controls.load('filter', el, function (el, fn) {
                    el.addEventListener('click', function (event) {
                        event.stopPropagation();

                        fn(el, $form);
                    });
                });

                // I move it to another place.
                if ($box) {
                    $box.innerHTML = '';
                    $box.appendChild($filters);
                }

                $filters.style.display = JsCookie.get('ListFilters');

                // I create the form
                $form = $filters.querySelector('form');
                $form.addEventListener('submit', function (event) {
                    callback(event, $form)
                });
                JsFelem.load($form);
            }
        };

        // controls
        _controls.attachAll('filter', {
            sort: function (el) {
                let value = el.getAttribute('data-value');

                if ($form.order.value == value) {
                    $form.sort.value = $form.sort.value == 'desc' ? 'asc' : 'desc';
                }
                $form.order.value = value;

                JsFelem.submit($form);
            },

            search: function (el) {
                $form.search.value = el.getAttribute('data-value');

                let field = el.getAttribute('data-field');
                if (field && typeof $form.field == 'object') {
                    $form.field.value = field;
                }

                JsFelem.submit($form);
            },

            change: function (el) {
                let name = el.getAttribute('data-name');
                let value = el.getAttribute('data-value');

                if ($form[name].type == 'checkbox') {
                    $form[name].checked = value;
                } else {
                    $form[name].value = value;
                }

                JsFelem.submit($form);
            },
        });

        _controls.attachAll('list', {
            filters: function () {
                that.toggle();
            },

            'filters-reset': function () {
                that.reset();
            }
        });

        return that;
    }

    /**
     * Manages the list as a whole.
     * 
     * @param {HTMLElement} $box
     * @param {Function}    callback
     * @returns {Object}
     */
    function List($box, callback) {
        let onSuccess;
        let _options = {};
        let _page = 1;
        let _filtersData = {};
        let _data = null;
        let requiresData = false;

        function combine(a, b) {
            for (let i in b) {
                a[i] = (typeof a[i] == 'object')
                    ? combine(a[i], b[i])
                    : b[i];
            }

            return a;
        }

        return {
            setPage: function (page) {
                _page = page;
            },

            setFilters: function (data) {
                _filtersData = data;
            },

            setData: function (data) {
                _data ??= {};

                if (data === undefined) {
                    requiresData = true;
                } else {
                    _data = combine(_data, data);
                }
            },

            init: function (options, $U) {
                if (requiresData) {
                    let $form = this.getForm();

                    if ($form) {
                        Array.from($form.elements).forEach(function (el) {
                            if (el.type == 'hidden' && el.name && !el.disabled) {
                                _data[el.name] = el.value;
                            }
                        });
                    }
                }

                // create default request options
                switch (typeof options) {
                    case 'string':
                        _options.url = options;
                        break;
                    case 'object':
                        _options = options;
                        break;
                }

                if (!_options.url && $U) {
                    _options.url = $U('list');
                }

                _options.format = 'text';
                onSuccess = typeof _options.onSuccess == 'function' ? _options.onSuccess : false;
                _options.onSuccess = function (html) {
                    $box.innerHTML = html;
                    callback();
                };
            },

            request: function () {
                _options.data = [];

                if (_data !== null) {
                    _options.data.push(_data);
                }

                if (_page > 1) {
                    _options.data.push({ page: _page });
                }

                if (_filtersData) {
                    _options.data.push(_filtersData);
                }

                $box.focus();
                JsRequest.load(_options);
            },

            getForm: function (name) {
                $form = $box ? $box.querySelector('[backlist-form]') : null;

                if (name) {
                    return typeof $form[name] != 'undefined'
                        ? $form[name].value
                        : undefined;
                }

                return $form;
            },

            getHash: function () {
                return _page + (_filtersData ? '/' + _filtersData : '');
            },

            fireSuccess: function () {
                if (onSuccess) {
                    onSuccess($box);
                }
            },
        };
    }

    /**
     * Manages the back bottom system.
     * 
     * @param {List}   _list 
     * @param {Function} callback
     * @returns {Object}
     */
    function Hash(_list, callback) {
        function hash() {
            return window.self.document.location.hash;
        }

        let handler, current = '';
        let that = {
            load: function () {
                if (!handler) {
                    current = hash();
                    handler = setInterval(function () {
                        if (current != hash()) {
                            current = hash();
                            that.load();
                            callback();
                        }
                    }, 300);
                }

                let part = current.substring(1).split('/');
                let page = parseInt(part.shift()) || 1;
                let filters = part.join('/');

                _list.setPage(page);
                _list.setFilters(filters);
            },

            save: function () {
                if (handler) {
                    window.self.document.location.hash = _list.getHash();
                    current = hash();
                }

                return this;
            },
        };

        return that;
    }

    /**
     * Manages the options as a whole.
     * 
     * @param {HTMLElement} $box
     * @param {JsControls}  _controls
     * @param {Buttons}   _buttons
     * @returns {Object}
     */
    function Options($box, _controls, _buttons) {
        let $form;

        return {
            load: function () {
                let $options = $box.querySelector('div[backlist-actions]');

                if (!$options) {
                    return;
                }

                _controls.load('list', $options, function (el, fn, cmd) {
                    _buttons.add(cmd, el);

                    // set events
                    function fn2(event) {
                        event.preventDefault();

                        if (_buttons.isDisabled(el)) {
                            return;
                        }

                        JsDropdown.hide();
                        fn(el);
                    }

                    if (cmd == 'create') {
                        $form = el.parentNode;
                        if ($form.tagName == 'FORM') {
                            $form.addEventListener('submit', fn2);
                        } else {
                            $form = false;
                        }
                    }

                    el.addEventListener('click', fn2);
                });

                JsFelem.load($options);
            },

            getData: function (data) {
                if ($form) {
                    data.push($form);
                }
            }
        };
    }

    /**
     * Pagination
     * 
     * @param {HTMLElement} el 
     * @param {Function} callback
     */
    function _pagination(el, callback) {
        let bts = Array.from(el.querySelectorAll('.footer *[control-page]'));

        bts.forEach(function (bt) {
            bt.addEventListener('click', function (event) {
                let page = parseInt(bt.getAttribute('control-page'));
                callback(event, page);
            });
        });
    }

    /**
     * To Query String
     * 
     * @param {HTMLFormElement} $form 
     * @returns {String}
     */
    function toQueryString($form) {
        let data = [];

        Array.from($form.elements).forEach(function (el) {
            if (el.name && !el.disabled) {
                if (el.type == 'select-multiple') {
                    for (let i = 0; i < el.options.length; i++) {
                        if (el.options[i].selected) {
                            data.push(el.name + '=' + el.options[i].value);
                        }
                    }
                } else if (['checkbox', 'radio'].indexOf(el.type) == -1 || el.checked) {
                    data.push(el.name + '=' + el.value);
                }
            }
        });

        return data.join('&');
    }

    /**
     * Transform an object into a function.
     * 
     * @param {Object} options 
     * @param {String} cmd 
     * @returns {Function}
     */
    function objToFn(options, cmd) {
        if (typeof options.modalOptions == 'object') {
            options.load = 'modal';
        } else if (typeof options.load == 'undefined') {
            options.load = 'xjs';
        }

        if (!options.url && $U) {
            options.url = $U(cmd);
        }

        let isEditable = (typeof options.editable != 'undefined' && options.editable);
        let fn = function (el) {
            if (typeof options.onSubmit == 'function' && !options.onSubmit(el)) {
                return;
            }

            if (isEditable && el.style.display != 'none') {
                el.style.display = 'none';
                let value = el.innerHTML;
                let input = el.parentNode.appendChild(JsElement('input.input-field', {
                    name: cmd,
                    value: value,
                    events: {
                        click: function (event) {
                            event.stopPropagation();
                        },
                        keydown: function (event) {
                            if (event.key == 'Enter') {
                                event.preventDefault();
                                input.blur();
                            }
                        },
                        blur: function () {
                            if (this.value == value) {
                                el.style.display = '';
                                input.parentNode.removeChild(input);
                            } else {
                                fn(el);
                            }
                        }
                    },
                }));
                input.focus();

                return false;
            }

            // request
            let _options = Object.assign({}, options);
            let data = [];
            let $form = _list.getForm();

            if ($form) {
                data.push($form);
            }

            if (cmd == 'create' && !options.numRows) {
                _listOptions.getData(data);
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
            _options.data = data;

            JsRequest.load(_options);
        };

        return fn;
    }

    /**
     * Main
     */
    let $U, _initialized;
    let $box = document.getElementById((ID ? ID + '-' : '') + 'backlist-box');
    let $list = $box?.querySelector('div[backlist-slot]');

    if ($box) {
        JsNotify.creator($box);
    }

    let _controls = JsControls({
        list: {
            refresh: function () {
                that.refresh();
            },

            view: function (el) {
                JsDropdown(el.nextSibling).toggle();
            },

            'tg-body': function (el) {
                el.parentNode.classList.toggle('expand');
            },
        }
    });

    let _rows = Rows(_controls);
    let _buttons = _rows.getButtons();
    let _filters = Filters(_controls, function (event, data) {
        if (event) {
            event.preventDefault();
        }

        _list.setPage(1);
        _list.setFilters(data ? toQueryString(data) : '');
        _hash.save();
        that.load();
    });

    let _list = List($list, function () { that.async() });
    let _hash = Hash(_list, function () { that.load() });
    let _listOptions = Options($box, _controls, _buttons);

    let that = {
        url: function (fn) {
            if (typeof fn == 'function') {
                $U = fn;
            }
            return this;
        },

        data: function (data) {
            _list.setData(data);
            return this;
        },

        allowHistory: function () {
            _hash.load();
            return this;
        },

        controls: function (controls, handler) {
            if (!handler) {
                handler = 'list';
            }

            for (let cmd in controls) {
                (function (options) {
                    switch (typeof options) {
                        case 'object':
                            if (handler != 'list') {
                                break;
                            }
                            /* if (target && typeof options.modalOptions == 'object') {
                                options.modalOptions.target = target;
                            } */
                            if (typeof options.numRows == 'undefined') {
                                options.numRows = (cmd == 'create') ? 0 : '+';
                            }

                            _buttons.addCmd(cmd, options.numRows, options.onlyRows);

                            // get function
                            if (typeof options.fn == 'function') {
                                options = options.fn;
                            } else {
                                options = objToFn(options, cmd);
                            }
                        // break;

                        case 'function':
                            _controls.attach(handler, cmd, options);
                    }
                })(controls[cmd]);
            }

            return this;
        },

        setControlsWithTarget(controls, target) {
            if (target) {
                for (let cmd in controls) {
                    if (typeof controls[cmd] === 'object'
                        && typeof controls[cmd].modalOptions === 'object'
                    ) {
                        controls[cmd].modalOptions.target = target;
                    }
                }
            }

            return this.controls(controls);
        },

        load: function (options) {
            if (!$box) {
                return;
            }

            let _request = true;

            if (!_initialized) {
                _initialized = true;
                _request = !$list.innerHTML;

                _filters.setBox($box);
                _list.init(options, $U);
                _listOptions.load();
                Tooltip($box);
            }

            _rows.restart();

            if (_request) {
                _list.request();
            } else {
                this.async();
            }

            return this;
        },

        async: function () {
            let $form = _list.getForm();

            if ($form) {
                _buttons.clean();
                _controls.load('list', $form, function (el, fn, cmd) {
                    _buttons.add(cmd, el);

                    el.addEventListener('click', function (event) {
                        event.stopPropagation();

                        if (_buttons.isVirtuallyDisabled(el)) {
                            return;
                        }

                        _rows.select(el);

                        fn(el);
                    });
                });

                _controls.load('row', $form);
                _buttons.refresh();
                _pagination($list, function (event, page) {
                    _list.setPage(page);
                    _hash.save();
                    that.load();
                });
                _filters.load($list);
                _list.fireSuccess();

                Tooltip($list);
            }

            return this;
        },

        refresh: function () {
            this.load();
        },

        getElement: function (selector) {
            if (selector) {
                return $box.querySelector(selector);
            }

            return $box;
        },

        getForm: function (name) {
            return _list.getForm(name);
        },

        getFormValue: function (name) {
            if (!name) {
                return null;
            }

            return _list.getForm(name);
        },

        getFiltersData: function () {
            let h = location.hash.split('/')[1];
            let data = {};

            if (h) {
                h.split('&').forEach(function (row) {
                    row = row.split('=');
                    data[row[0]] = row[1];
                });
            }

            return data;
        },

        notify: function (options) {
            $box.notify(options);
        },
    };

    return that;
};
