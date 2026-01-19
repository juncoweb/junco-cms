
/* --- Cookie Consent --- */
window.addEventListener('load', function () {
    const el = document.querySelector('#cookieconsent');
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