
/* --- JobsFailures ------------------------------------------ */
let JobsFailures = (function () {
    function $U(task) {
        return JsUrl('admin/jobs.failures/' + task);
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

    let _backlist, target;
    let _controls = {
        show: {
            numRows: '1',
            modalOptions: {
                size: 'large',
                onLoad: function () {

                },
            },
        },

        status: {
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
