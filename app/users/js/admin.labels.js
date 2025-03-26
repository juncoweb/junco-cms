
/* --- UsersLabels ----------------------------------- */
let UsersLabels = (function () {
    function $U(task, output) {
        return JsUrl('admin/users.labels/' + task, false, output);
    }

    return {
        List: function () {
            function callback(message, code) {
                if (code) {
                    if (target) {
                        target = target.close();
                    }
                    _backlist.refresh();
                }
                (target || _backlist).notify(message);
            }

            let target;
            let mo = {
                //size:'large',
                onLoad: function () {
                    target = this;
                    JsForm({ btn: this }).request($U('save'), callback);
                },
            };

            let _backlist = Backlist()
                .url($U)
                .controls({
                    create: {
                        onlyRows: 'editable',
                        modalOptions: mo,
                    },

                    edit: {
                        onlyRows: 'editable',
                        modalOptions: mo,
                    },

                    confirm_delete: {
                        onlyRows: 'editable',
                        modalOptions: {
                            onLoad: function () {
                                target = this;
                                JsForm({ btn: this }).request($U('delete'), callback);
                            },
                        },
                    },
                })
                .allowHistory()
                .load();
        },
    };
})();
