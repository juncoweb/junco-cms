
/* --- Notifications ------------------------------------------ */
const JsNotifications = (function () {
    const key = 'notifications';

    return {
        show: function () {
            localStorage.removeItem(key);
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
        },

        load: function (el) {
            const lifetime = minutes(20);
            const badge = el.querySelector('span');

            function minutes(total) {
                return total * (60 * 1000);
            }

            function show(total) {
                total = parseInt(total);
                if (total) {
                    badge.textContent = total;
                }

                badge.style.display = total ? '' : 'none';
            }

            function get() {
                let data = localStorage.getItem(key);

                if (!data) {
                    return null;
                }

                data = JSON.parse(data);

                return data && (data.createdAt + lifetime) > Date.now()
                    ? data
                    : null;
            }

            function request() {
                JsRequest.json({
                    url: JsUrl('my/notifications/data'),
                    spinner: false,
                    onSuccess: function (json) {
                        json.createdAt = Date.now();
                        localStorage.setItem(key, JSON.stringify(json));
                        show(json.total);
                    }
                });
            }

            const data = get(key);

            if (data) {
                show(data.total);
            } else {
                request();
            }
        }
    };
})();