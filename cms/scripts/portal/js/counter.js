// --- counter ------------------------------ */
JsScroll.addEvent('counter', function () {
    let all = this.querySelectorAll('[data-counter]');
    if (all) {
        all.forEach(function (el) {
            let top = el.getAttribute('data-counter');
            let num = 0;
            let duration = 1200;
            let steps = 60;
            let increment = top / steps;

            let handle = setInterval(function () {
                if (num >= top) {
                    el.innerHTML = top;
                    clearInterval(handle);
                } else {
                    num += increment;
                    el.innerHTML = parseInt(num);
                }
            }, duration / steps);
        });
    }
    return true;
});