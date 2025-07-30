/* --- Settings -------------------------------------------- */
let Settings = (function () {
    function $U(task, data) {
        return JsUrl('admin/settings/' + task, data);
    }

    function callback(res) {
        if (res.ok()) {
            if (target) {
                target = target.close();
            }
            if (res.code == 2) {
                setTimeout(function () {
                    window.location.reload();
                }, 1000);
            } else {
                setTimeout(Load, 1000);
            }
        }

        (target || $box).notify(res.message);
    }

    function _data() {
        return {
            key: _form.getForm().__key.value
        };
    }

    function Restore() {
        let data = {};
        let values = JSON.parse(document.getElementById('restore').value);

        for (let name in values) {
            let value = values[name];
            if (typeof value === 'object') {
                value = JSON.stringify(value);
            }
            data[name] = [null, value];
        }

        function getValue(el) {
            if (el.type == 'checkbox') {
                return el.checked;
            } else if (el.type == 'select-multiple') {
                let v = [];
                for (let i = 0, l = length; i < l; i++) {
                    if (el.options[i].selected) {
                        v.push(el.options[i].value);
                    }
                }
                return v;
            }

            return el.value;
        }

        function getStatus(bt) {
            let status = parseInt(bt.getAttribute('data-restore-status')) ? 0 : 1;
            let icon = bt.querySelector('i').classList;

            bt.setAttribute('data-restore-status', status);
            if (status) {
                icon.replace('fa-wand-magic', 'fa-rotate-left');
            } else {
                icon.replace('fa-rotate-left', 'fa-wand-magic');
            }
            return status;
        }

        return {
            toggle: function (bt) {
                let status = getStatus(bt);
                let name = bt.getAttribute('data-restore');
                let form = document.getElementById('settings-form');
                let el = form[name] || form.querySelector('[data-name=' + name + ']');

                // save current
                if (status) {
                    data[name][0] = getValue(el);
                }

                switch (el.type) {
                    case 'checkbox':
                        el.checked = data[name][status];
                        break;
                    case 'select-multiple':
                        let j = 0,
                            l = el.options.length;
                        for (; j < l; j++) {
                            el.options[j].selected = (-1 != data[name][status].indexOf(el.options[j].value));
                        }
                        break;
                    case 'suite':
                        let value = data[name][status];
                        try {
                            value = JSON.parse(value || '[]').join(',');
                        } catch (e) { }
                        el.reset(value);
                        break;
                    default:
                        el.value = data[name][status];
                        break;
                }
            }
        };
    }

    let target, _form, $restore;
    let _goback = history && history.pushState;
    let $box = document.querySelector('#settings-box');
    let _controls = {
        refresh: function () {
            Load();
        },

        edit: {
            url: $U('edit'),
            data: _data,
            modalOptions: {
                size: 'large',
                onLoad: function () {
                    target = this;
                    JsForm().request($U('update'), callback);
                },
            },
        },

        delete: {
            url: $U('confirm_delete'),
            data: _data,
            modalOptions: {
                //size: 'large',
                onLoad: function () {
                    target = this;
                    JsForm().request($U('delete'), callback);
                },
            },
        },

        prepare: {
            url: $U('prepare'),
            data: _data,
            modalOptions: {
                onLoad: function () {
                    target = this;
                    JsForm({ btn: this }).request({
                        url: $U('edit'),
                        modalOptions: {
                            size: 'large',
                            onLoad: function () {
                                target.close();
                                target = this;
                                JsForm().request($U('update'), callback);
                            },
                        },
                    }, false, 'modal');
                },
            },
        },

        restore: function (el) {
            if ($restore == null) {
                $restore = Restore();
            }

            $restore.toggle(el);
        },

        load: function (el) {
            if (el.href) {
                Load(el.href, true);
            }
        },
    };

    function Load(url, save) {
        if (_goback && save) {
            window.top.history.pushState({
                path: url
            },
                window.self.document.title,
                url
            );
        }

        JsRequest.text({
            url: url || document.location.href,
            onSuccess: function (html) {
                $box.innerHTML = html;
                $restore = null;
                _form = JsForm('#settings-form');

                if (_form) {
                    _form
                        .controls(_controls)
                        .request($U('take'), callback);
                }
            },
        });
    }

    function Menus() {
        let A = [];
        function Select(el) {
            let i = A.length;
            while (i--) {
                A[i].className = '';
            }
            el.className = 'selected';
        }
        document.querySelectorAll('.widget-thirdbar ul > li > ul a').forEach(function (el) {
            A.push(el);
            el.addEventListener('click', function (event) {
                event.preventDefault();
                Select(el);
                Load(el.href, true);
                document.body.click();
            });
        });
    }

    /**
     * Methods
     */
    return {
        Load: function (key) {
            Load($U('', {
                key: key
            }));
            Menus();

            $box = JsNotify.creator($box, $box.parentNode);
            if (_goback) {
                window.addEventListener('popstate', function () {
                    Load();
                });
            }
        },

        refresh: function () {
            Load();
        },

        notify: function (options) {
            $box.notify(options);
        },

        setControls: function (controls) {
            for (let i in controls) {
                _controls[i] = controls[i];
            }
        },

        send: function () {
            _form.submit();
        }
    };
})();


/**
 * Tools
 */
function JsonFormElement($btn) {
    function findForm(el) {
        for (; el.tagName != 'FORM'; el = el.parentNode);
        return el;
    }
    function findName(el) {
        for (; !el.getAttribute('data-set'); el = el.parentNode);
        return el.getAttribute('data-set');
    }
    function parseValue(value) {
        try {
            return JSON.parse(value);
        } catch (e) { }
        return value;
    }

    const isEdit = $btn.getAttribute('data-json') == 'edit';
    const $element = findForm($btn, 'FORM')[findName($btn)];
    let _rows;

    return {
        toggle: function () {
            $element.style.display = $element.style.display == '' ? 'none' : '';
        },
        getValue: function () {
            _rows = JSON.parse($element.value || 'false');
            return JSON.stringify(_rows);
        },
        getOptions: function () {
            return $element.getAttribute('data-options') || '';
        },
        isMultiple: function () {
            return Boolean(this.getOptions());
        },
        isEdit: function () {
            return isEdit;
        },
        compile: function (el) {
            const inputs = findForm(el).querySelectorAll('input');
            _rows = isEdit
                ? {} // restart
                : (_rows || {});

            if (this.isMultiple()) {
                let __id = {};
                inputs.forEach(function (el) {
                    let match = el.name.match(/^(.*?)\[(.*?)\]$/);
                    if (match[1] == '__id') {
                        __id[match[2]] = parseValue(el.value);
                    } else {
                        _rows[__id[match[2]]] ??= {};
                        _rows[__id[match[2]]][match[1]] = parseValue(el.value);
                    }
                });
            } else if (isEdit) {
                inputs.forEach(el => _rows[el.name] = parseValue(el.value));
            } else {
                _rows[inputs[0].value] = parseValue(inputs[1].value);
            }

            $element.value = JSON.stringify(_rows);
        }
    };
};

(function () {
    let target, current;
    let _controls = {
        remove: function (el) {
            (FormRow(el) || FormFieldset(el)).remove();
        },

        finish: function (el) {
            current.compile(el);
            target = target.close();
        },

        'finish-save': function (el) {
            _controls.finish(el);
            Settings.send();
        }
    };

    function $U(task, output) {
        return JsUrl('admin/settings/' + task, false, output);
    }

    // controls
    Settings.setControls({
        toggle: function (el) {
            JsonFormElement(el).toggle();
        },

        json: {
            url: $U('json'),
            onSubmit: function (el) {
                try {
                    current = JsonFormElement(el);
                    const json = current.getValue();
                    this.data = {};

                    if (current.isMultiple()) {
                        this.data.options = current.getOptions();
                    }
                    if (current.isEdit()) {
                        if (!json) {
                            return false;
                        }
                        this.data.json = json;
                    }
                } catch (e) {
                    alert('Json Error!');
                    return false;
                }
                return true;
            },
            modalOptions: {
                size: 'large',
                onLoad: function () {
                    target = this;
                    JsForm({ btn: this })
                        .controls(_controls)
                        .request();
                }
            },
        },
    });
})();