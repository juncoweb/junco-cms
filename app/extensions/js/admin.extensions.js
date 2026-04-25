
/* --- Extensions ---------------------------------------- */
let Extensions = (function () {
    function $U(task) {
        return JsUrl('admin/extensions/' + task);
    }

    function callback(res) {
        if (res.ok()) {
            if (target) {
                target = target.close();
            }
            _backlist.refresh();
        }
        (target || _backlist).notify(res.message);
    }

    function compile(_target) {
        let f = JsForm({ btn: _target });
        if (f) {
            if (!f.getForm().output) {
                f.request({
                    url: $U('confirm_compile'),
                    modalOptions: {
                        onLoad: function () {
                            target.close();
                            target = compile(this);
                        },
                    },
                }, false, 'modal');
            } else {
                f.request($U('compile'), callback);
            }
        }

        return _target;
    }

    let _backlist, target;
    let mo = {
        size: 'large',
        onLoad: function () {
            target = this;
            JsForm({ btn: this }).request($U('save'), callback);
        },
    };

    let _controls = {
        show: {
            numRows: '1',
            modalOptions: {
                size: 'large',
            }
        },
        edit: {
            numRows: '1',
            modalOptions: mo,
        },
        create: {
            modalOptions: mo,
        },
        confirm_status: {
            onlyRows: 'owner',
            modalOptions: {
                onLoad: function () {
                    target = this;
                    JsForm({ btn: this }).request($U('status'), callback);
                },
            },
        },
        confirm_delete: {
            modalOptions: {
                onLoad: function () {
                    target = this;
                    JsForm({ btn: this }).request($U('delete'), callback);
                },
            },
        },
        confirm_dbhistory: {
            numRows: '1',
            modalOptions: {
                size: 'large',
                onLoad: function () {
                    target = this;
                    JsForm({ btn: this }).request($U('dbhistory'), callback);
                },
            },
        },
        edit_readme: {
            numRows: '1',
            modalOptions: {
                size: 'large',
                onLoad: function () {
                    target = this;
                    JsForm({ btn: this }).request($U('update_readme'), callback);
                },
            },
        },
        confirm_append: {
            numRows: '1',
            onlyRows: 'package',
            modalOptions: {
                onLoad: function () {
                    target = this;
                    JsForm({ btn: this }).request($U('append'), callback);
                },
            },
        },
        confirm_compile: {
            onlyRows: 'package',
            modalOptions: {
                onLoad: function () {
                    target = compile(this);
                },
            },
        },
        distribute: {
            data: { format: 'blank' },
            load: 'blank'
        }
    };

    return {
        List: function () {
            if (!_backlist) {
                _backlist = Backlist()
                    .url($U)
                    .controls(_controls)
                    .allowHistory()
                    .load();
            }
            return _backlist;
        },

        setControls: function (controls) {
            for (let i in controls) {
                _controls[i] = controls[i];
            }
        },
    };
})();
