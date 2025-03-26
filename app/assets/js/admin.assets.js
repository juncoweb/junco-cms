
/* --- Assets ------------------------------------------ */
let AdminAssets = (function () {
    function $U(task) {
        return JsUrl('admin/assets/' + task, false);
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
        size: 'large',
        onLoad: function () {
            target = this;
            JsForm().request($U('save'), callback);
            JsTabs('#assets-tabs', {
                'onSelect': function () {
                    const el = this.getContainer().querySelector('textarea');
                    if (typeof el.autoGrow === 'function') {
                        el.autoGrow();
                    }
                }
            }).select();
        },
    };

    let _controls = {
        create: {
            modalOptions: mo,
        },

        edit: {
            numRows: '1',
            modalOptions: mo,
        },

        cwu: {
            onSuccess: callback,
        },

        inspect: {
            onSuccess: callback,
        },

        confirm_delete: {
            modalOptions: {
                onLoad: function () {
                    target = this;
                    JsForm({ btn: this }).request($U('delete'), callback);
                },
            },
        },

        confirm_compile: {
            numRows: '+',
            modalOptions: {
                onLoad: function () {
                    target = this;
                    JsForm({ btn: this }).request($U('compile'), callback);
                },
            },
        },

        confirm_options: {
            numRows: '*',
            modalOptions: {
                //size: 'large',
                onLoad: function () {
                    target = this;
                    JsForm({ btn: this }).request($U('options'), callback);
                },
            },
        },
    };


    return {
        List: function () {
            if (_backlist) {
                return _backlist;
            }
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
