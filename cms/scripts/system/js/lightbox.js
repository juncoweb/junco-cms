/**
 * Lightbox
 */
function Lightbox(options) {
    options = Object.assign({
        iniVisible: true,
        hideWithButton: true,
        hideWithOverlay: true,
        valignCenter: true,
        onShow: null,
        onHide: null,
        onToggle: null
    }, options);

    const body = document.body;
    const overlay = body.appendChild(JsElement('div.lightbox', { html: '<div></div>' }));
    const box = overlay.firstChild;
    const _focus = JsFocus(overlay, {
        onEscape: function () {
            that.hide();
        }
    });
    const that = {
        remove: function () {
            overlay.parentNode.removeChild(overlay);
            body.classList.remove('lightbox-fixed');
        },

        toggle: function (force) {
            const status = body.classList.toggle('lightbox-fixed', force);
            overlay.style.display = status ? '' : 'none';
            _focus.toggle(status);

            if (status) {
                options.onShow?.call(that);
            } else {
                options.onHide?.call(that);
            }
            options.onHide?.call(that, status);

            return status;
        },

        show: function () {
            this.toggle(true);
        },

        hide: function () {
            this.toggle(false);
        },

        getContainer: function () {
            return box;
        },

        setContent: function (content) {
            if (typeof content === 'string') {
                box.innerHTML = content;
            } else {
                box.innerHTML = '';
                box.appendChild(content);
            }

            return this;
        }
    };

    if (options.valignCenter) {
        overlay.classList.add('valign-center');
    }

    if (options.hideWithButton) {
        overlay
            .appendChild(JsElement('button.lightbox-cross btn-inline', {
                html: '<i class="fa-solid fa-xmark" aria-hidden="true"></i>',
                'aria-label': 'Close'
            }))
            .addEventListener('click', function (event) {
                event.stopPropagation();
                that.hide();
            });
    }

    if (options.hideWithOverlay) {
        overlay.addEventListener('click', function (event) {
            event.stopPropagation();
            that.hide();
        });

        box.addEventListener('click', function (event) {
            event.stopPropagation();
        });
    }

    that.toggle(options.iniVisible);

    return that;
};
