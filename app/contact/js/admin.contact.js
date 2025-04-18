
/* --- Contact ------------------------------------------- */
var Contact = (function () {
    function $U(task) {
        return JsUrl('admin/contact/' + task);
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
            let _backlist = Backlist()
                .url($U)
                .controls({
                    status: {
                        onSuccess: callback,
                    },

                    show: {
                        numRows: '1',
                        modalOptions: {
                            size: 'large',
                        }
                    },

                    confirm_delete: {
                        modalOptions: {
                            onLoad: function () {
                                target = this;
                                JsForm({ btn: this }).request($U('delete'), callback);
                            },
                        },
                    }
                })
                .allowHistory()
                .load();
        },
    };
})();
