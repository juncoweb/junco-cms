
/* --- Coockie ----------------------------------------------------- */
var JsCookie = (function () {
    function set(key, value, attr) {
        let cookie = key + '=' + decodeURIComponent(value);

        // Attributes
        attr = Object.assign({ path: '/', expires: 365 }, attr);

        if (typeof attr.expires === 'number') {
            attr.expires = new Date(Date.now() + attr.expires * 864e5);
        }

        if (attr.expires) {
            attr.expires = attr.expires.toUTCString();
        }

        for (let name in attr) {
            if (attr[name]) {
                cookie += '; ' + name;

                if (attr[name] !== true) {
                    cookie += '=' + attr[name].split(';')[0];
                }
            }
        }

        // return
        return (window.top.document.cookie = cookie);
    }

    function get(key) {
        let match = window.top.document.cookie.match(key + '=([^;]*)');
        if (!match) {
            return '';
        }

        let value = match[1];

        if (value[0] === '"') {
            value = value.slice(1, -1);
        }

        return decodeURIComponent(value);
    }

    return Object.create({
        set: set,
        get: get,
        remove: function (key, attr) {
            set(key, '', Object.assign({}, attr, { expires: -1 }));
        }
    });
})();
