
/* --- Frontend --- */
const Frontend = (function () {
    const controls = JsControls({ tpl: {} });

    return {
        attach: function (name, fn) {
            controls.attach('tpl', name, fn);
        },

        attachAll: function (obj) {
            controls.attachAll('tpl', obj);
        },

        load: function (box) {
            controls.load('tpl', box);
        }
    };
})();

Frontend.attachAll({
    logout: function (el) {
        el.addEventListener('click', function (event) {
            UsysLogout();
        });
    },
    theme: JsTheme,

    language: function ($btn) {
        //const current = document.documentElement.lang;
        $btn.parentNode.querySelectorAll('[data-value]').forEach(function (el) {
            const lang = el.getAttribute('data-value');

            el.addEventListener('click', function () {
                JsRequest.xjs({
                    url: JsUrl('language/change'),
                    data: { lang },
                    onSuccess: function (res) {
                        if (res.ok()) {
                            window.location.reload();
                        } else {
                            alert(res.message);
                        }
                    },
                });
            });
        });
    },
    notifications: function (el) {
        JsNotifications.load(el);
        el.addEventListener('click', function () {
            JsNotifications.show();
        });
    },
    search: (function () {
        let box;
        return function (el) {
            el.setAttribute('role', 'button');
            el.addEventListener('click', function (event) {
                event.preventDefault();

                if (!box) {
                    box = Lightbox({
                        onShow: function () {
                            this.getContainer().querySelector('input')?.focus();
                        }
                    }).setContent('<div style="width: 100%; max-width: 900px;">'
                        + '<form class="box-default p-8 rounded-large" action="' + el.href + '" method="GET">'
                        + '<div class="input-group input-large">'
                        + '<input type="input" name="q" placeholder="" class="input-field input-primary">'
                        + '<button type="submit" class="btn btn-primary btn-solid"><i class="fa-solid fa-magnifying-glass"></i></button>'
                        + '</form>'
                        + '</div>');
                }
                box.show();
            });
        };
    })(),
});


window.addEventListener('DOMContentLoaded', function () {
    Navbar('.navbar', 'header .pull-btn');
    ActiveHeader('body.fixed-header .tpl-header');
    Frontend.load(document);

    let h = document.body.querySelector('.tpl-header');
    if (h) {
        JsFelem.load(h);
    }
});