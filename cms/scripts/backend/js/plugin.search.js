/* --- search --- */
Backend.attach('search', function ($input) {
    let status = 0;
    let data = null;
    const $menu = $input.parentNode.appendChild(JsElement('div', {
        html: '<div class="dropdown-menu"><ul id="search-result"></ul></div>',
        hide: function (status) {
            this.style.display = status ? 'none' : '';
        }
    }));
    const $list = $menu.querySelector('ul');
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

        $list.innerHTML = html;
        $menu.hide(html == '');
    }

    $menu.hide(true);
    $input.setAttribute('aria-controls', 'search-result');
    $input.addEventListener('input', function () {
        if (status === 0) {
            status = 1;
            JsRequest.json({
                url: JsUrl('admin/backend/menus'),
                onSuccess: function (json) {
                    data = json;
                    _print($input.value);
                    status = 2;
                }
            });
        } else if (status == 2) {
            _print($input.value);
        }
    });
});