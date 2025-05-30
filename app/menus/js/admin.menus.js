
/* --- Menus -------------------------------------- */
let Menus = (function () {
    function $U(task) {
        return JsUrl('admin/menus/' + task);
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
                size: 'large',
                onLoad: function () {
                    target = this;
                    JsForm({ btn: this }).request($U('save'), callback);
                },
            };

            let _backlist = Backlist()
                .url($U)
                .controls({
                    create: {
                        modalOptions: mo,
                    },
                    edit: {
                        modalOptions: mo,
                    },
                    copy: {
                        numRows: '1',
                        modalOptions: mo,
                    },
                    status: {
                        onSuccess: callback,
                    },
                    lock: {
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
                    confirm_maker: {
                        numRows: '*',
                        modalOptions: {
                            onLoad: function () {
                                target = this;
                                JsForm({ btn: this }).request($U('maker'), callback);
                            },
                        },
                    },
                })
                .allowHistory()
                .load();
        },
    };
})();
