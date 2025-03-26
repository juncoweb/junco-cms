
/* --- Frontend ---------------------------- */
var Frontend = (function () {
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
                    onSuccess: function (message, code) {
                        if (code) {
                            window.location.reload();
                        } else {
                            alert(message);
                        }
                    },
                });
            });
        });
    },

    notifications: function (el) {
        el.addEventListener('click', function (event) {
            Notifications();
        });
    },
    search: (function () {
        let box;
        return function (el) {
            el.addEventListener('click', function (event) {
                event.preventDefault();
                if (!box) {
                    box = document.body.appendChild(JsElement('DIV.tpl-search', {
                        html: '<div><i class="fa-solid fa-xmark cursor-pointer"></i></div>'
                            + '<form class="box-default p-8" action="' + el.href + '" method="GET">'
                            + '<div class="input-group input-large">'
                            + '<input type="input" name="q" placeholder="" class="input-field input-primary">'
                            + '<button type="submit" class="btn btn-primary btn-solid"><i class="fa-solid fa-magnifying-glass"></i></button>'
                            + '</div>'
                            + '</form>'
                    }));

                    box.querySelector('div').addEventListener('click', function () { box.toggle(); });
                    box.toggle = function () {
                        if (document.body.classList.toggle('search-fixed')) {
                            this.querySelector('input').focus();
                        }
                    };

                }
                box.toggle();
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

window.addEventListener('load', function () {
    var el = document.querySelector('#cookieconsent');
    if (el) {
        if (!JsCookie.get('cookieConsent')) {
            el.classList.add('visible');
            document.querySelector('#cc-btn').addEventListener('click', function () {
                el.classList.remove('visible');
                JsCookie.set('cookieConsent', 1);
            });
        }
    }
});