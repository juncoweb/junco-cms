/**
 * Lightbox
 */
function Lightbox(options) {
    options = Object.assign({
        iniVisible: true,
        hideWithButton: true,
        hideWithOverlay: true,
        valignCenter: true,
    }, options);

    const body = document.body;
    const overlay = body.appendChild(JsElement('div.lightbox', { html: '<div></div>' }));
    const box = overlay.firstChild;
    const that = {
        remove: function () {
            overlay.parentNode.removeChild(overlay);
            body.classList.remove('lightbox-fixed');
        },

        toggle: function (force) {
            const status = body.classList.toggle('lightbox-fixed', force);
            overlay.style.display = status ? '' : 'none';

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
            .appendChild(JsElement('i.fa-solid fa-xmark lightbox-cross'))
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
