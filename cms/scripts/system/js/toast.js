/**
 * Toast
 *
 * @author: Junco CMS (tm)
 */

var JsToast = (function () {
    var box;
    return function (options) {
        if (typeof options == 'string') {
            options = { message: options };
        }

        options = Object.assign({
            message: '',
            type: '',
            displayLength: 8
        }, options);

        if (!box) {
            box = document.body.appendChild(JsElement('div.toast-container'));
        }

        var toast = box.appendChild(JsElement('div', {
            html: '<div class="toast-body">' + options.message + '</div><div class="toast-close" data-toast="close"><i class="fa-solid fa-xmark"></i></div>',
            className: 'toast' + (options.type ? ' toast-' + options.type : ''),
            role: 'alert',
            close: function () {
                box.removeChild(this);
                if (!box.firstChild) {
                    box.parentNode.removeChild(box);
                    box = null;
                }
                handler = false;
            }
        }));

        toast.querySelector('[data-toast=close]').addEventListener('click', function () {
            toast.close();
        });

        //
        if (options.displayLength) {
            var handler = setTimeout(function () {
                if (handler) {
                    toast.close();
                }
            }, options.displayLength * 1000)
        }

        return toast;
    };
})();