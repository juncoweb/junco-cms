
/* --- Installer ---------------------------------------- */
(function () {
    function $U(task) {
        return JsUrl('admin/system/' + task);
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

    Installer.setControls({
        confirm_maintenance: {
            numRows: '*',
            modalOptions: {
                size: 'small',
                onLoad: function () {
                    target = this;
                    JsForm({ btn: this }).request($U('maintenance'), callback);
                },
            },
        }
    });
})();
