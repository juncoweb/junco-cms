
/* --- Extensions ---------------------------------------- */
let Extensions = (function () {
    function $U(task) {
        return JsUrl('admin/extensions/' + task);
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

    function compile(_target) {
        let f = JsForm({ btn: _target });
        if (f) {
            if (!f.getForm().output) {
                f.request({
                    url: $U('confirm_compile'),
                    modalOptions: {
                        onLoad: function () {
                            target.close();
                            target = compile(this);
                        },
                    },
                }, false, 'modal');
            } else {
                f.request($U('compile'), callback);
            }
        }

        return _target;
    }

    function getContent(i, value) {
        switch (i) {
            case 5: return '<a href="' + value + '" target="_blank">' + value + '</a>';
            case 6:
                let x = '';
                for (let j in value) {
                    x += '<div title="' + value[j] + '" class="badge badge-primary text-uppercase">' + j + '</div>';
                }
                return x;

            case 7: return value.split(',').join(',<br />');
            case 8: return value.split(',').join(', ');
        }
        return value;
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
        edit: {
            numRows: '1',
            modalOptions: mo,
        },

        create: {
            modalOptions: mo,
        },

        confirm_status: {
            onlyRows: 'owner',
            modalOptions: {
                onLoad: function () {
                    target = this;
                    JsForm({ btn: this }).request($U('status'), callback);
                },
            },
        },

        confirm_delete: {
            modalOptions: {
                onLoad: function () {
                    target = this;
                    JsForm({ btn: this }).request($U('delete'), callback);
                },
            },
        },

        confirm_dbhistory: {
            numRows: '1',
            modalOptions: {
                size: 'large',
                onLoad: function () {
                    target = this;
                    JsForm({ btn: this }).request($U('dbhistory'), callback);
                },
            },
        },

        edit_readme: {
            numRows: '1',
            modalOptions: {
                size: 'large',
                onLoad: function () {
                    target = this;
                    JsForm({ btn: this }).request($U('update_readme'), callback);
                },
            },
        },

        details: function (el) {
            try {
                let html = '';
                let caption = JSON.parse(document.getElementById('details-caption').innerHTML);
                let options = JSON.parse(document.getElementById(el.getAttribute('data-value')).innerHTML);

                for (let i = 0, L = options.content.length; i < L; i++) {
                    if (options.content[i]) {
                        html += '<tr><th>' + caption[i] + ':</th><td>' + getContent(i, options.content[i]) + '</td></tr>';
                    }
                }
                options.content = '<table class="table table-condensed table-auto">' + html + '</table>';
                Modal(options).show();
            } catch (e) {
                alert(e);
            }
        },

        confirm_append: {
            numRows: '1',
            onlyRows: 'package',
            modalOptions: {
                onLoad: function () {
                    target = this;
                    JsForm({ btn: this }).request($U('append'), callback);
                },
            },
        },

        confirm_compile: {
            onlyRows: 'package',
            modalOptions: {
                onLoad: function () {
                    target = compile(this);
                },
            },
        },

        distribute: {
            data: { format: 'blank' },
            load: 'blank'
        },
    };

    return {
        List: function () {
            if (!_backlist) {
                _backlist = Backlist()
                    .url($U)
                    .controls(_controls)
                    .allowHistory()
                    .load();
            }
            return _backlist;
        },

        setControls: function (controls) {
            for (let i in controls) {
                _controls[i] = controls[i];
            }
        },
    };
})();
