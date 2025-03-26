/* --- Console ----------------------------------------------------- */
var JsConsole = (function () {
    function $U(args) {
        return JsUrl('/system.console', args, 'blank');
    }

    var cookieName = 'JsConsole';
    var options = {
        mode: 0,
    };

    var _window;
    var _tails = [];

    // functions
    function consolePopup(report) {
        if (!_window || _window.closed) {
            try {
                _window = window.open('', 'system-console', 'width=580,height=480,toolbar=no,scrollbars=no,top=500,left=500');

                if (_window.location == 'about:blank') {
                    _window.location = $U();
                }
            } catch (e) {
                alert('The navigator lockout the popup! ' + e);
                return;
            }
        }

        if (typeof _window.JsConsole == 'object') {
            _window.JsConsole.log(report);
        } else {
            _tails.push(report);
        }
    }

    function consoleFrame(report) {
        if (window.top == window.self) {
            window.stop();
            window.location = $U({
                frame: window.location.href
            });
        } else {
            _window = window.parent;
            _window.JsConsole.log(report);
            _window.history.replaceState({
                path: window.self.location.href
            },
                window.self.document.title,
                window.self.location.href
            );
        }
    }

    function setOption(key, value) {
        options[key] = value;
        JsCookie.set(cookieName, JSON.stringify(options));
    }

    /**
     * Methods
     */
    return {
        getTails: function () {
            return _tails;
        },

        toggle: function () {
            setOption('mode', options.mode ? 0 : 1);
            (_window.opener || _window).location.reload();

            if (_window.opener) {
                _window.close();
            }
        },

        log: function (report) {
            if (typeof report != 'object') {
                report = {
                    content: report,
                    title: 'Report'
                };
            }

            switch (options.mode) {
                case 1:
                    consoleFrame(report);
                    break;
                default:
                    consolePopup(report);
                    break;
            }
        },

        getTextData: function (html) {
            var a = html.indexOf('<!--{profiler}');
            if (a > -1) {
                var b = html.indexOf('-->', a);
                if (b) {
                    return html.slice(html.indexOf('}', a) + 1, b);
                }
            }
            return null;
        },

        load: function (e) {
            var html = document.querySelector('console').innerHTML;
            var cookie = JsCookie.get(cookieName);

            if (cookie) {
                try {
                    options = Object.assign(options, JSON.parse(cookie));
                } catch (e) { }
            }

            this.log(this.getTextData(html));
        },
    };
})();