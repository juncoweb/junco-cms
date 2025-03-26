
/* --- Installer ---------------------------------------- */
(function () {
    function $U(task) {
        return JsUrl('admin/system/' + task);
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
