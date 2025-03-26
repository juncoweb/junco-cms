
/* --- Color ------------------------------------------------ */
function JsColor(el) {
    if (el.tagName != 'INPUT') {
        return;
    }
    let btn = JsElement('input', {
        type: 'color',
        value: el.value,
        className: 'input-field input-color',
        events: {
            change: function () {
                el.value = btn.value;
            }
        }
    });

    el.type = 'text';
    el.addEventListener('input', function () { btn.value = el.value; });
    let group = el.parentNode.insertBefore(JsElement('div.input-group'), el);
    group.appendChild(el)
    group.appendChild(btn);
}

JsFelem.implement({
    'color': function (el, box) {
        JsColor(el);
    }
});