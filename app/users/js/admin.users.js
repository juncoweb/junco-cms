
/* --- users ------------------------------- */
var Users = (function () {
    function $U(task) {
        return JsUrl('admin/users/' + task);
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
            JsForm({ btn: this }).request($U('save'), callback);
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
        status: {
            data: 'get-data',
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

    JsFelem.implement({
        roles: function (el, f) {
            JsCollection(el, {
                url: JsUrl('admin/users/roles')
            });
        },
    });

    return {
        List: function () {
            _backlist = Backlist()
                .url($U)
                .controls(_controls)
                .allowHistory()
                .load();
        }
    };
})();
