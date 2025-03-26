
/* --- Notifications ------------------------------------------ */
Notifications = (function () {
    return function () {
        JsRequest.text({
            url: JsUrl('my/notifications/show'),
            onSuccess: function (html) {
                let el = document.querySelector('[control-tpl=notifications] span');
                if (el) {
                    el.parentNode.removeChild(el);
                }
                Lightbox({ valignCenter: false }).setContent(html);
            },
        })
    };
})();
