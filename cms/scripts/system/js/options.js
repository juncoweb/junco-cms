
/**
 * Options
 */
var JsOptions = (function () {
    var _options = {};

    return function (key) {
        if (_options[key] === undefined) {
            _options[key] = {};
        }

        return {
            get: function (defaultOptions) {
                if (typeof defaultOptions == 'object') {
                    _options[key] = Object.assign({}, defaultOptions, _options[key]);
                }

                return _options[key];
            },

            set: function (options) {
                if (typeof options == 'object') {
                    for (var i in options) {
                        _options[key][i] = options[i];
                    }
                }
            }
        };
    };
})();