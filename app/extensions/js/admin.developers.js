
/* --- Developers --------------------------------------------------------- */
(function () {
    function $U(task) {
        return JsUrl('admin/extensions.developers/' + task);
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
        let _backlist = Backlist('developers')
            .url($U)
            .controls({
                edit: {
                    numRows: '1',
                    modalOptions: mo,
                },

                create: {
                    modalOptions: mo,
                },

                confirm_delete: {
                    numRows: '1',
                    modalOptions: {
                        target: target,
                        onLoad: function () {
                            target_2 = this;
                            JsForm({ btn: this })?.request($U('delete'), callback);
                        },
                    },
                },
            })
            .load();
    };

    Extensions.setControls({
        developers: {
            url: $U('index'),
            numRows: '*',
            modalOptions: {
                size: 'large',
                onLoad: function () {
                    List(this);
                }
            },
        },
    });
})();
