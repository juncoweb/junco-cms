/**
 * Move
 *
 * @author: Junco CMS (tm)
 * @param: el  (HtmlElement)
 * @param: startFn  (function)
 * @param: moveFn  (function)
 * @param: endFn  (function)
 */

var JsMove = function (el, startFn, moveFn, endFn) {
    function setClient(event) {
        var e = event;
        switch (event.type) {
            case 'touchstart': e = event.touches[0]; break;
            case 'touchmove':
            case 'touchend': e = event.changedTouches[0]; break;
        }

        if (e.clientX || e.clientY) {
            if (e !== event) {
                event.clientX = e.clientX;
                event.clientY = e.clientY;
            }
        } else if (e.pageX || e.pageY) {
            event.clientX = e.pageX - document.body.scrollLeft - document.documentElement.scrollLeft;
            event.clientY = e.pageY - document.body.scrollTop - document.documentElement.scrollTop;
        }
    };

    function setEvent(el, eventNames, fn, force) {
        eventNames.split(' ').forEach(function (eventName) {
            el[(force ? 'add' : 'remove') + 'EventListener'](eventName, fn);
        });
    };

    function activate(force) {
        setEvent(document, 'mousemove touchmove', _moveFn, force);
        setEvent(document, 'mouseup touchend', _endFn, force);
    };

    function _startFn(event) {
        setClient(event);
        startFn(event);
        activate(true);
    };

    function _moveFn(event) {
        setClient(event);
        moveFn(event);
    };

    function _endFn(event) {
        if (typeof endFn == 'function') {
            setClient(event);
            endFn(event);
        }
        activate(false);
    };

    setEvent(el, 'mousedown touchstart', _startFn, true);
};