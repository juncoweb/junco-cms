
/* --- Translations -------------------------------------------- */
(function () {
    function $U(task) {
        return JsUrl('admin/language.translations/' + task);
    }

    function List(target) {
        function callback(res) {
            if (res.ok()) {
                if (target_2) {
                    target_2 = target_2.close();
                }
                _backlist.refresh();
            }
            (target_2 || _backlist).notify(res.message);
        }

        let target_2;
        let _backlist = Backlist('translations')
            .url($U)
            .controls({
                confirm_download: {
                    url: $U('confirm_download'),
                    modalOptions: {
                        target: target,
                        onLoad: function () {
                            target_2 = this;
                            JsForm({ btn: this }).request($U('download'), callback);
                        },
                    },
                },
            })
            .data()
            .load();
    }

    Language.setControls({
        translations: {
            url: $U('index'),
            numRows: '?',
            modalOptions: {
                size: 'large',
                onLoad: function () {
                    List(this);
                },
            },
        }
    });
})();
