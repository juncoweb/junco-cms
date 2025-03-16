
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
				return function (json) {
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

					fn.call(options, json.message, json.code, json.data);
				}
			})(options.onSuccess),
		});
	},
});
