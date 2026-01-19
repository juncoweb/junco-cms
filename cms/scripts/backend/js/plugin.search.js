/* --- search ------------------------------------------ */
Backend.attach('search', function (el) {
    let status = 0;
    let data = null;
    const dd = el.parentNode.appendChild(JsElement('div'));
    const QR = {
        'a': '[aáàâä]',
        'e': '[eéèêë]',
        'i': '[iíìîï]',
        'o': '[oóòôö]',
        'u': '[uúùûü]',
        'n': '[nñ]'
    };
    const types = ['fa-regular fa-window-maximize', 'fa-solid fa-gear'];

    function getRegExp(re) {
        for (let i in QR) {
            re = re.replace(RegExp(i, 'gi'), QR[i]);
        }
        return RegExp(re, 'i');
    }

    function _print(value) {
        let html = '';
        if (value) {
            const regex = getRegExp(value);
            data.forEach(row => {
                if (row[1].search(regex) != -1) {
                    html += '<li><a href="' + row[2] + '"><i class="' + types[row[0]] + ' color-light"></i>' + row[1] + '</a></li>';
                }
            });
        }
        if (html) {
            html = '<div class="dropdown-menu"><ul>' + html + '</ul></div>';
            dd.innerHTML = html;
        } else {
            dd.innerHTML = '';
        }
    }

    el.addEventListener('input', function () {
        if (status === 0) {
            status = 1;
            JsRequest.json({
                url: JsUrl('admin/backend/menus'),
                onSuccess: function (json) {
                    data = json;
                    _print(el.value);
                    status = 2;
                }
            });
        } else if (status == 2) {
            _print(el.value);
        }
    });
});