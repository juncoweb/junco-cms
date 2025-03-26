
/* --- Tools -------------------------------------------------- */
let AdminTools = (function () {
    function $U(task) {
        return JsUrl('admin/samples/' + task);
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

            let _backlist = Backlist()
                .url($U)
                .controls({
                    edit: {
                        numRows: '1',
                        modalOptions: {
                            size: 'large',
                            onLoad: function () {
                                target = this;
                                JsForm({ btn: this }).request($U('update'), callback);
                            },
                        },
                    },
                })
                .allowHistory()
                .load();
        },
    };
})();
