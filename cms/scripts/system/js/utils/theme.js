/* --- Theme ------------------------------------------ */
const JsTheme = function ($btn) {
    const storageName = 'prefers-color-scheme';
    const currentTheme = localStorage.getItem(storageName) || 'auto';

    function getTheme(mode) {
        if (mode === 'auto') {
            return window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches
                ? 'dark'
                : 'light';
        }

        return mode;
    }

    function set(mode) {
        document.documentElement.setAttribute('data-theme', getTheme(mode));
        ['dark', 'light', 'auto', 'hidden'].forEach((m) => document.body.classList.toggle(`mode-${m}`, m == mode));
    }

    $btn.parentNode.querySelectorAll('[data-value]').forEach(function ($el) {
        const mode = $el.getAttribute('data-value');

        if (currentTheme === mode) {
            set(mode);
        }

        $el.addEventListener('click', function () {
            localStorage.setItem(storageName, mode);
            document.body.click();
            set(mode);
        });
    });
};