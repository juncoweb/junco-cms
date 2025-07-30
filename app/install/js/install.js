
/* --- Install ----------------------------------------------- */
var Install = (function () {
    function $U(task) {
        return JsUrl('/install/' + task);
    }

    function _next() {
        window.location = $U(wizart(1));
    }

    function _notify(message) {
        JsNotify({
            message: message,
            target: document.getElementById('notify')
        });
    }

    var wizart = (function () {
        var steps = document.querySelectorAll('.wizard li');
        return function (sum) {
            for (var i = 0; i < steps.length && steps[i].className; i++);
            return steps[i - 1 + sum].getAttribute('data-step');
        }
    })();

    var _controls = JsControls({
        install: {
            back: function () {
                window.location = $U(wizart(-1));
            },

            next: _next,

            refresh: function () {
                window.location.reload();
            },

            submit: function () {
                JsRequest.xjs({
                    url: $U('take_' + wizart(0)),
                    data: document.getElementById('js-form'),
                    onSuccess: function (res) {
                        if (res.code == 1) {
                            _next();
                        } else {
                            _notify(res.message);
                        }
                    },
                });
            },

            language: function (el) {
                JsRequest.xjs({
                    url: $U('take_language'),
                    data: { 'lang': el.getAttribute('data-value') },
                    onSuccess: function (res) {
                        if (res.ok()) {
                            setTimeout('window.location.reload();', 2000);
                        }
                        _notify(res.message);
                    },
                });
            },
        },
    });

    window.addEventListener('load', function () {
        _controls.load('install', document, function (el, fn) {
            el.addEventListener('click', function () {
                fn(el);
            });
        });
    });
})();
