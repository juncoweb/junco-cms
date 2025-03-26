/* --- Topbar ------------------------------------------ */
Backend.attachAll({
    notifications: function (el) {
        el.addEventListener('click', function () {
            Notifications();
        });
    },
    logout: function (el) {
        el.addEventListener('click', function () {
            UsysLogout();
        });
    },
    theme: JsTheme,
});