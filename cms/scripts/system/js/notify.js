/**
 * Notify
 *
 * @author: Junco CMS (tm)
 * @options:
 *  - message (string) the text content
 *  - target (element or string) the text content
 *  - class (string) the class of the first child
 *  - maxTime (number defaults to 50) maximum exposure time, so as not to hide put 0
 *  - minTime (number defaults to 8)
 */
const JsNotify = (function () {
    let current;

    function notify(options) {
        let handler, handler2;
        let _hits = 0;
        const MAX_HITS = 100;

        // functions
        function clear() {
            options.target.innerHTML = '';
            current = null;
        }

        function hit() {
            hide(1);
        }

        function hide(hits, force) {
            if (!(options.maxTime > 0)) {
                return;
            }

            _hits += hits;
            if (_hits > MAX_HITS) {
                document.removeEventListener('click', hit);
                handler = clearTimeout(handler);

                if (force) {
                    clear();
                } else {
                    handler = setTimeout(clear, 1000);
                }
            } else if (_hits == MAX_HITS) {
                handler = setTimeout(hit, options.maxTime * 1000);
            }
        }

        function copyUp() {
            handler2 = setTimeout(() => {
                try {
                    navigator.clipboard.writeText($span.innerHTML);
                    JsToast({ message: 'Copied!', type: 'success' });
                    hide(MAX_HITS + 1, true);
                } catch (err) {
                    JsToast({ message: 'Failed', type: 'error' });
                }
            }, 1500)
        }

        function copyDown() {
            handler2 = clearTimeout(handler2)
        }

        //
        options = Object.assign({
            message: '',
            class: '',
            maxTime: 42,
            minTime: 6,
            target: null,
        }, options);

        if (typeof options.target == 'string') {
            options.target = document.querySelector(options.target);
        }
        if (typeof options.target != 'object') {
            alert(options.message);
        }
        if (options.class) {
            options.class = ' class="' + options.class + '"';
        }

        options.target.innerHTML = '<span><span' + options.class + '>' + options.message + '</span></span>';

        const $span = options.target.querySelector('span > span');
        $span.addEventListener('click', () => hide(MAX_HITS + 1, true));
        $span.addEventListener('mousedown', copyUp);
        $span.addEventListener('touchstart', copyUp);
        $span.addEventListener('mouseup', copyDown);
        $span.addEventListener('touchend', copyDown);

        if (options.maxTime) {
            document.addEventListener('click', hit);
            handler = setTimeout(() => hide(MAX_HITS), options.minTime * 1000);
        }

        return {
            hide: function () {
                hide(MAX_HITS + 1, true)
            }
        };
    }

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

JsNotify.creator = function (element, before) {
    if (typeof element == 'string') {
        element = document.querySelector(element);
    }
    if (typeof element.notify != 'function') {
        before ??= element;
        const $target = before.parentNode.insertBefore(JsElement('div.notify-box', { role: 'alert' }), before);

        element.notify = function (options) {
            if (typeof options == 'string') {
                options = { message: options };
            }
            options.target = $target;
            JsNotify(options);
        };
    }
    return element;
};