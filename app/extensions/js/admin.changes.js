
/* --- Changes --------------------------------------------------------- */
(function () {
    function $U(task) {
        return JsUrl('admin/extensions.changes/' + task);
    }

    function List(target) {
        function callback(message, code) {
            if (code) {
                if (target_2) {
                    target_2 = target_2.close();
                }
                _backlist.refresh();
            }
            (target_2 || target).notify(message);
        }

        let target_2;
        let mo = {
            size: 'large',
            target: target,
            onLoad: function () {
                target_2 = this;
                JsForm({ btn: this }).request($U('save'), callback);
            },
        };

        let _backlist = Backlist('changes');
        _backlist.url($U)
            .controls({
                create: {
                    modalOptions: mo,
                },

                edit: {
                    numRows: '1',
                    modalOptions: mo,
                },

                confirm_delete: {
                    modalOptions: {
                        target: target,
                        onLoad: function () {
                            target_2 = this;
                            JsForm({ btn: this }).request($U('delete'), callback);
                        },
                    },
                },
            })
            //.allowHistory()
            .data({ extension_id: _backlist.getFormValue('extension_id') })
            .load();
    };

    Extensions.setControls({
        changes: {
            url: $U('index'),
            numRows: '1',
            modalOptions: {
                size: 'large',
                onLoad: function () {
                    List(this);
                }
            },
        },
    });
})();

