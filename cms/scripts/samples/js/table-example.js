
/*
 * Demo
 */

function Submit(ev, el) {
    ev.preventDefault();
    eval(el.code.value);
}

function autoGrow(e) {
    if (!autoGrow.instance[e])
        autoGrow.instance[e] = e.rows;

    var
        r = autoGrow.instance[e],
        n = e.value.split("\n"),
        l = n.length;

    if (e.cols) {
        for (i in n)
            if (n[i].length > e.cols)
                l += Math.floor(n[i].length / e.cols);
    }

    if (l > (r - 1)) {
        e.rows = l + 1;
    } else if (l < r) {
        e.rows = r;
    }
}
autoGrow.instance = [];

/*
 * On Load
 */
window.addEventListener('load', function () {
    var el = document.getElementsByTagName('FORM'),
        i = el.length;
    while (i--) {
        el[i].addEventListener('submit', (function (e) { return function (ev) { Submit(ev, e) } })(el[i]));
    }

    var el = document.getElementsByTagName('TEXTAREA'),
        i = el.length;
    while (i--) {
        el[i].addEventListener('keyup', (function (e) { return function () { autoGrow(e) } })(el[i]));
        autoGrow(el[i]);
    }
});

