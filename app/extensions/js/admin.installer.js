
/* --- Installer ---------------------------------------- */
let Installer = (function () {
    function $U(task) {
        return JsUrl('admin/extensions.installer/' + task);
    }

    function setCleanerEvents(clean) {
        let a = clean.querySelector('#clean-all');
        let b = clean.querySelectorAll('input[name="clean_paths[]"]');
        let L = b.length;
        a.addEventListener('change', function () {
            for (let i = 0; i < L; i++) {
                b[i].checked = a.checked;
            }
        });

        for (let i = 0; i < L; i++) {
            b[i].addEventListener('change', function () {
                if (this.checked) {
                    let x = 0;
                    for (let i = 0; i < L; i++) {
                        if (b[i].checked == true) {
                            x++;
                        }
                    }
                    a.checked = L == x;
                } else {
                    a.checked = false;
                }
            });
        }
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
    let _controls = {
        confirm_install: {
            modalOptions: {
                size: 'large',
                onLoad: function () {
                    target = this;
                    JsTabs('#installer-tabs')?.select();

                    let clean = target.getElement().querySelector('.ei-clean');
                    if (clean) {
                        setCleanerEvents(clean);
                    }
                    JsForm({ btn: this }).request($U('install'), callback);
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
        confirm_find_updates: {
            numRows: '*',
            modalOptions: {
                size: 'small',
                onLoad: function () {
                    target = this;
                    JsForm({ btn: this }).request($U('find_updates'), callback);
                },
            },
        },
        confirm_update_all: {
            numRows: '*',
            modalOptions: {
                size: 'small',
                onLoad: function () {
                    target = this;
                    JsForm({ btn: this }).request($U('update_all'), callback);
                },
            },
        },
        confirm_upload: {
            numRows: '*',
            modalOptions: {
                onLoad: function () {
                    target = this;
                    JsForm({ btn: this }).request($U('upload'), callback);
                },
            },
        },
        confirm_download: {
            modalOptions: {
                onLoad: function () {
                    target = this;
                    let f = JsForm({ btn: this });
                    if (f) {
                        f.request($U('download'), callback);
                    }
                }
            },
        },
        confirm_unzip: {
            modalOptions: {
                size: 'small',
                onLoad: function () {
                    target = this;
                    JsForm({ btn: this }).request($U('unzip'), callback);
                }
            },
        },
        confirm_maintenance: {
            numRows: '*',
            modalOptions: {
                size: 'small',
                onLoad: function () {
                    target = this;
                    JsForm({ btn: this }).request($U('maintenance'), callback);
                },
            },
        },
        show_failure: {
            onlyRows: 'failed',
            numRows: '1',
            modalOptions: {
                //size: 'large',
                onLoad: function () {
                    target = this;
                },
            },
        }
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


/* --- I set up the extension controls ------------------- */
(function () {
    let _backlist, target;

    function $U(task) {
        return JsUrl('admin/extensions.installer/' + task);
    }

    function callback(message, code) {
        if (!_backlist) {
            _backlist = Extensions.List();
        }
        if (code) {
            if (target) {
                target = target.close();
            }
            _backlist.refresh();
        }
        (target || _backlist).notify(message);
    };

    Extensions.setControls({
        confirm_find_updates: {
            url: $U('confirm_find_updates'),
            numRows: '*',
            modalOptions: {
                size: 'small',
                onLoad: function () {
                    target = this;
                    JsForm({ btn: this }).request($U('find_updates'), callback);
                }
            },
        },
        update: {
            url: $U('confirm_update'),
            numRows: '1',
            modalOptions: {
                size: 'small',
                onLoad: function () {
                    target = this;
                    JsForm({ btn: this }).request($U('update'), callback);
                }
            },
        },
        confirm_update_all: {
            url: $U('confirm_update_all'),
            numRows: '*',
            modalOptions: {
                size: 'small',
                onLoad: function () {
                    target = this;
                    JsForm({ btn: this }).request($U('update_all'), callback);
                },
            },
        },
    });
})();
