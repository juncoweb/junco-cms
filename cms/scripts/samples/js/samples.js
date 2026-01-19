function JsSamples(box) {
    function getRow(el) {
        while (!el.classList.contains('sample-row')) {
            el = el.parentNode;
        }
        return el;
    }

    function jscode(el) {
        return getRow(el).querySelector('.sample-panel-2 form textarea').value;
    }

    function clipboardCopy(text) {
        try {
            navigator.clipboard.writeText(text);
            JsToast({ message: 'Copied!', type: 'success' });
        } catch (err) {
            JsToast({ message: 'Failed', type: 'error' });
        }
    }

    JsControls({
        sample: {
            runjs: function (el) {
                eval(jscode(el));
            },

            resetjs: function (el) {
                const code = getRow(el)
                    .querySelector('.sample-panel-2 form')
                    .reset();
            },

            copyjs: function (el) {
                clipboardCopy(jscode(el));
            },

            copy: function (el) {
                const code = getRow(el).querySelector('.sample-panel-1').innerHTML;
                clipboardCopy(code);
            },

            toggle: function (el) {
                getRow(el).classList.toggle('sample-toggle');
            },
        }
    }).load('sample', box || document, function (el, fn) {
        el.addEventListener('click', function (event) {
            event.preventDefault();
            fn(el);
        });
    });

    JsFelem.load('.samples-wrapper');
}
