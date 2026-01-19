/* --- Topbar ------------------------------------------ */
Backend.attachAll({
    notifications: function (el) {
        JsNotifications.load(el);
        el.addEventListener('click', function () {
            JsNotifications.show();
        });
    },
    logout: function (el) {
        el.addEventListener('click', function () {
            UsysLogout();
        });
    },
    theme: JsTheme,
    color: function (el) {
        ThemeColor.set(el);
    }
});

const ThemeColor = (function () {
    return {
        set: function ($btn) {
            const header = document.querySelector('.layout-header');
            const colors = ['default', 'primary', 'secondary', 'info', 'success', 'warning', 'danger'];

            function set(color) {
                colors.forEach((c) => header.classList.toggle(`header-${c}`, c == color));
                JsCookie.set(header.dataset.tck, color);
            }

            $btn.parentNode.querySelectorAll('[data-value]').forEach(function ($el) {
                const color = $el.getAttribute('data-value');

                $el.addEventListener('click', function () {
                    document.body.click();
                    set(color);
                });
            });
        }
    };
})();