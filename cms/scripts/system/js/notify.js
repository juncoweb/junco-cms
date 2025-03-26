/**
 * Notify
 *
 * @author: Junco CMS (tm)
 * @options:
 * message - (string) the text content
 * target - (object or string) the text content
 * class - (string) the class of the first child
 * maxTime - (number defaults to 50) maximum exposure time, so as not to hide put 0
 * minTime - (number defaults to 8)
 */

var JsNotify = (function () {
    var current;
    var notify = function (options) {
        // vars
        var handler;
        var _hits = 0;

        // functions
        function clear() {
            options.target.innerHTML = '';
            current = null;
        }

        function hit() {
            hide(1);
        }

        function hide(hits, already) {
            if (!(options.maxTime > 0)) {
                return;
            }

            _hits += hits;
            if (_hits > 100) {
                document.removeEventListener('click', hit);
                handler = clearTimeout(handler);

                if (already) {
                    clear();
                } else {
                    handler = setTimeout(clear, 1000);
                }
            } else if (_hits == 100) {
                handler = setTimeout(hit, options.maxTime * 1000);
            }
        }

        // set options
        options = Object.assign({
            message: '',
            class: '',
            maxTime: 42,
            minTime: 8,
            target: null,
        }, options);

        //
        if (typeof options.target == 'string') {
            options.target = document.querySelector(options.target);
        }
        if (typeof options.target != 'object') {
            alert(options.message);
        }

        options.target.innerHTML = '<span><span'
            + (options['class'] ? ' class="' + options['class'] + '"' : '') + '>'
            + options.message
            + '</span></span>';

        if (options.maxTime) {
            document.addEventListener('click', hit);
            handler = setTimeout(function () { hide(100); }, options.minTime * 1000);
        }

        return {
            hide: function () {
                hide(101, true);
            }
        };
    };

    return function (options) {
        if (current != null) {
            current.hide();
        }
        if (options) {
            current = notify(options);
        }
    };
})();

JsNotify.hide = function () {
    JsNotify(false);
};

JsNotify.creator = function (object, before) {

    if (typeof object.notify != 'function') {
        if (!before) {
            before = object;
        }
        var _target = before.parentNode.insertBefore(JsElement('div.notify-box', { role: 'alert' }), before);

        object.notify = function (options) {
            if (typeof options == 'string') {
                options = { 'message': options };
            }
            options.target = _target;
            JsNotify(options);
        };
    }
    return object;
};