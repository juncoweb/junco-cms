
/* --- Permissions ------------------------------------ */
var Permissions = (function () {
    function $U(task) {
        return JsUrl('admin/users.permissions/' + task);
    }

    return {
        List: function () {
            function callback(res) {
                if (res.ok()) {
                    _backlist.refresh();
                }
                _backlist.notify(res.message);
            };
            let _backlist = Backlist()
                .url($U)
                .controls({
                    status: {
                        onSuccess: callback,
                    },
                })
                .allowHistory()
                .load();
        },
    };
})();
