
/* --- users ------------------------------- */
let UsersActivities = (function () {
    function $U(task) {
        return JsUrl('admin/users.activities/' + task);
    }

    return {
        List: function () {
            function callback(res) {
                if (res.ok()) {
                    if (target) {
                        target = target.close();
                    }
                    _backlist.refresh();
                }
                (target || _backlist).notify(res.message);
            }

            let target;
            let _backlist = Backlist()
                .url($U)
                .controls({
                    confirm_delete: {
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
