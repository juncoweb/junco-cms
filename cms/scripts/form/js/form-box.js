/**
 * Form Box
 */
function FormBox(box) {
    if (!box) {
        box = 0;
    }
    switch (typeof box) {
        default: box = '';
        case 'number': box = '#form-box' + (box || '');
        case 'string':
            box = document.querySelector(box) || document.querySelector('#' + box + '-box');
        case 'object':
            if (!box) {
                return;
            }
    }

    // vars
    let _controls = JsControls({
        form: {
            'nav-prev': function () {
                that.select(selected - 1);
            },

            'nav-next': function () {
                that.select(selected + 1);
            },

            'refresh': function () {
                that.refresh();
            },
        },
    });
    let tabs = box.firstChild;
    if (tabs.classList.contains('tablist')) {
        tabs = JsTabs(tabs);
    } else {
        tabs = false;
    }
    let tabOptions = [];
    let isloaded = false;
    let selected = 0;
    let panel = box;

    function _update(el, options, force) {
        if (!options) {
            return;
        }
        function _onLoad() {
            _controls.load('form', el, function (el, fn) {
                el.addEventListener('click', function () {
                    fn();
                });
            });
            if (typeof options.onLoad == 'function') {
                options.onLoad();
            }
        }

        if (!force && el.firstChild) {
            if (!isloaded) {
                isloaded = true;
                _onLoad();
            }
        } else {
            options.onSuccess = function (html) {
                el.innerHTML = html;
                _onLoad();
            };
            JsRequest.text(options);
        }
    }

    let that = {
        controls: function (options) {
            for (let i in options) {
                if (typeof options[i] == 'function') {
                    _controls.attach('form', i, options[i]);
                }
            }
            return this;
        },
        tab: function (options) {
            tabOptions.push(options);
            return this;
        },
        select: function (i, force) {
            if (tabs) {
                tabs.select(i);
                panel = tabs.getContainer();
                selected = tabs.selectedTabNumber();
            }
            _update(panel, tabOptions[selected], force);
            return this;
        },
        refresh: function () {
            this.select(selected, true);
        },
    };

    JsNotify.creator(that, box);

    return that;
}
