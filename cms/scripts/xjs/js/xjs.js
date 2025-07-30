
/**
 * Implement framework methods
 */
JsRequest.implement({
    xjs: function (options) {
        return this.ajax(options, {
            format: 'xjs',
            responseType: 'json',
            update: false,
            onSuccess: (function (fn) {
                return function (json, response) {
                    if (typeof json.data == 'object') {
                        if (json.data.__reloadPage) {
                            window.location.reload();
                        }
                        if (typeof json.data.__redirectTo == 'string') {
                            window.location = new URL(json.data.__redirectTo, window.location.origin).href;
                        }
                        if (json.data.__goBack) {
                            window.history.go(-1);
                        }
                    }

                    fn.call(options, JsResult(json, response));
                }
            })(options.onSuccess),
        });
    },
});

const JsResult = function (json, response) {
    json.ok = function () {
        if (json.code) {
            if (!response.ok) {
                console.log('Error in the http status code.');
            }
            return true;
        }
        if (response.ok) {
            console.log('The status "unprosesable" must be returned via http.');
        }

        return false;
    };

    return json;
};