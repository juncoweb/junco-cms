
/* --- Color --- */
function JsColor($input) {
    if ($input.tagName != 'INPUT') {
        return;
    }
    const $btn = JsElement('input', {
        type: 'color',
        value: $input.value,
        className: 'input-field input-color',
        events: {
            change: function () {
                $input.value = $btn.value;
                $input.dispatchEvent(new Event('input', { bubbles: true }));
                $input.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }
    });

    $input.type = 'text';
    $input.addEventListener('input', function () {
        if ($input.value.match(/^\#[0-9a-f]{6}$/i)) {
            $btn.value = $input.value;
        }
    });
    const $group = $input.parentNode.insertBefore(JsElement('div.input-group'), $input);
    $group.appendChild($input)
    $group.appendChild($btn);
}

JsFelem.implement({
    'color': function (el, box) {
        JsColor(el);
    }
});