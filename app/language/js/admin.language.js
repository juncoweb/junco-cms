
/* --- Language ------------------------------------------ */
let Language = (function () {
    function $U(task, format) {
        return JsUrl('admin/language/' + task, {}, format);
    }

    function callback(message, code) {
        if (code) {
            if (target) {
                target = target.close();
            }
            _backlist.refresh();
        }
        (target || _backlist).notify(message);
    }

    let _backlist, target;
    let mo = {
        onLoad: function () {
            target = this;
            JsForm({ btn: this }).request($U('save'), callback);
        },
    },

        _controls = {
            create: {
                modalOptions: mo,
            },
            edit: {
                numRows: '1',
                modalOptions: mo,
            },
            confirm_duplicate: {
                numRows: '1',
                modalOptions: {
                    onLoad: function () {
                        target = this;
                        JsForm({ btn: this }).request($U('duplicate'), callback);
                    },
                },
            },
            confirm_select: {
                onlyRows: 'enabled',
                numRows: '1',
                modalOptions: {
                    onLoad: function () {
                        target = this;
                        JsForm({ btn: this }).request($U('select'), function (message, code) {
                            if (code) {
                                window.location.reload();
                            } else {
                                (target || _backlist).notify(message);
                            }
                        });
                    },
                },
            },
            status: {
                numRows: '1',
                onSuccess: callback
            },
            confirm_delete: {
                modalOptions: {
                    onLoad: function () {
                        target = this;
                        JsForm({ btn: this }).request($U('delete'), callback);
                    },
                },
            },
            confirm_import: {
                numRows: '*',
                modalOptions: {
                    onLoad: function () {
                        target = this;
                        JsForm({ btn: this }).request($U('import'), callback);
                    },
                },
            },
            confirm_export: {
                numRows: '1',
                url: $U('export', 'blank'),
                load: 'get'
            },
            confirm_refresh: {
                numRows: '*',
                modalOptions: {
                    onLoad: function () {
                        target = this;
                        JsForm({ btn: this }).request($U('refresh'), callback);
                    },
                },
            },
            confirm_distribute: {
                numRows: '1',
                modalOptions: {
                    onLoad: function () {
                        target = this;
                        JsForm({ btn: this }).request($U('distribute'), callback);
                    },
                },
            },
        };

    return {
        List: function () {
            _backlist = Backlist()
                .url($U)
                .controls(_controls)
                .allowHistory()
                .load();
        },

        setControls: function (controls) {
            for (let i in controls) {
                _controls[i] = controls[i];
            }
        },
    };
})();
