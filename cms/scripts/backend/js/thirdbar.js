
/* --- Thirdbar ------------------------------------------ */
Backend.attach('thirdbar', function (bar) {
    (document.querySelectorAll('.widget-thirdbar ul > li > a') || []).forEach(function ($btn) {
        const $menu = $btn.nextSibling && $btn.nextSibling.tagName === 'UL'
            ? $btn.nextSibling
            : null;

        if ($menu) {
            $btn.setAttribute('aria-expanded', false);
            $btn.addEventListener('click', function (event) {
                event.preventDefault();
                event.stopPropagation();

                JsDropdown($menu, {
                    onToggle: function (status) {
                        $menu.classList.toggle('active', status);
                        $btn.setAttribute('aria-expanded', status);
                    },
                }).toggle();
            });
        }
    });
});