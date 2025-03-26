
/* --- Domains -------------------------------------------- */
(function () {
    function $U(task) {
        return JsUrl('admin/language.domains/' + task);
    }

    function callback(message, code) {
        if (code) {
            if (target_2) {
                target_2 = target_2.close();
            }
            _backlist.refresh();
        }
        (target_2 || target).notify(message);
    }

    var target_2;
    var _controls = {
        create: {
            url: $U('create'),
            modalOptions: {
                size: 'large',
                onLoad: function () {
                    target_2 = this;
                    JsForm({ btn: this }).request($U('store'), callback);
                },
            },
        },
    };

    //
    Language.setControls({
        domains: {
            url: $U('index'),
            numRows: '1',
            modalOptions: {
                size: 'large',
                onLoad: function () {
                    _backlist = Backlist('domains')
                        .url($U)
                        .setControlsWithTarget(_controls, this)
                        .data()
                        .load();
                },
            },
        }
    });
})();

