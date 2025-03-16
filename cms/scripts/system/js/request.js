/**
 * Request
 */

var JsRequest = (function () {
	function getType(value) {
		let type = typeof value;
		if (type == 'object') {
			type = Object.prototype.toString.call(value).slice(8, -1);
			return (type.slice(0, 4) == 'HTML' ? type.slice(4, -7) : type).toLowerCase();
		}
		return type;
	}

	function toQueryArray(data, esc) {
		let _data = [];
		switch (getType(data)) {
			case 'string':
				data.replace(/([^?=&]+)(=([^&]*))?/g, function ($0, $1, $2, $3) {
					_data.push([$1, $3]);
				});
				break;

			case 'array':
				if (esc) {
					data.forEach(function (item) {
						_data.push(item);
					});
				} else {
					data.forEach(function (items) {
						items = toQueryArray(items, true);
						if (items.length) {
							Array.prototype.push.apply(_data, items);
						}
					});
				}
				break;

			case 'object':
				for (let i in data) {
					_data.push([i, data[i]]);
				}
				break;

			case 'function':
				return toQueryArray(data());

			/**
			 * Serialize form or element.
			 *
			 * @credits: http://stackoverflow.com/questions/11661187/form-serialize-javascript-no-framework
			 */
			case 'input':
			case 'textarea':
			case 'select':
			case 'button':
				data = { elements: [data] };
			// break;
			case 'form':
				let el;
				for (let i = 0; el = data.elements[i]; i++) {
					if (el.name
						&& !el.disabled
						&& ['file', 'reset'].indexOf(el.type) == -1
					) {
						if (el.type == 'select-multiple') {
							let m = el.options.length;
							for (let j = 0; j < m; j++) {
								if (el.options[j].selected) {
									_data.push([el.name, el.options[j].value]);
								}
							}
						} else if (['checkbox', 'radio'].indexOf(el.type) == -1 || el.checked) {
							_data.push([el.name, el.value]);
						}
					}
				}
				break;
		}

		return _data;
	}

	function toQueryString(data) {
		data = toQueryArray(data);
		if (data) {
			return data
				.map(function (a) {
					return encodeURIComponent(a[0]) + '=' + encodeURIComponent(a[1])
				})
				.join('&')
				.replace(/%20/g, '+');
		}
		return '';
	}

	function toFormData(data) {
		if (getType(data) == 'form') {
			return new FormData(data);
		}

		let f, fd;

		// seeking a form and formdata
		if (getType(data) == 'array') {
			for (let i = 0, L = data.length; i < L && !f && !fd; i++) {
				switch (getType(data[i])) {
					case 'form':
						f = data[i];
						data.splice(i, 1);
						break;

					case 'formdata':
						fd = data[i];
						data.splice(i, 1);
						break;
				}
			}
		}

		if (!fd) {
			fd = new FormData(f);
		}

		toQueryArray(data).forEach(function (dataItem) {
			switch (dataItem.length) {
				case 2:
					fd.append(dataItem[0], dataItem[1]);
					break;
				case 3:
					fd.append(dataItem[0], dataItem[1], dataItem[2]);
					break;
			}
		});

		return fd;
	}

	function toURLGet(url, data) {
		return url + (url.indexOf('?') == -1 ? '?' : '&') + toQueryString(data);
	}

	function parseUrlData(url) {
		var data = [];
		if (url.indexOf('?') != -1) {
			var a = url.split('?')[1].split('&');
			for (var i in a) {
				data.push(a[i].split('=').map(function (com) {
					return decodeURIComponent(com);
				}));
			}
		}

		return data;
	}

	// online
	function onlineToast() {
		JsToast({
			message: 'Online',
			type: 'success'
		});
	}

	function offlineToast() {
		JsToast({
			message: 'Offline',
			type: 'danger'
		});
	}
	var online = window.navigator.onLine;
	var offline_displayed = false;

	window.addEventListener('offline', function (event) {
		online = false;
	});

	window.addEventListener('online', function (event) {
		online = true;
		if (offline_displayed) {
			offline_displayed = false;
			onlineToast();
		}
	});

	/**
	 * Methods
	 */
	return {
		mergeData: function () {
			let data = [];
			for (let i = 0; i < arguments.length; i++) {
				if (Array.isArray(arguments[i])) {
					for (let j = 0; j < arguments[i].length; j++) {
						data.push(arguments[i][j]);
					}
				} else if (typeof arguments[i] != 'undefined') {
					data.push(arguments[i]);
				}
			}

			return data;
		},

		implement: function (obj) {
			for (let i in obj) {
				if (typeof obj[i] == 'function' && !this[i]) {
					this[i] = obj[i];
				}
			}
		},

		load: function (options) {
			let load = options.load || 'text';

			if (['mergeData', 'implement', 'load'].indexOf(load) == -1 && typeof this[load] == 'function') {
				this[load](options);
			}
		},

		link: function (options, forceOptions) {
			options = Object.assign({}, options, forceOptions);
			var el = document.body.appendChild(JsElement('a', {
				href: toURLGet(options.url, options.data),
				target: options.target || '_self',
				styles: {
					display: 'none'
				}
			}));

			el.click();
			document.body.removeChild(el);
		},

		http: function (options, forceOptions) {
			options = Object.assign({}, options, forceOptions);
			var data = parseUrlData(options.url);

			if (getType(options.data) == 'form') {
				var f = options.data;
			} else {
				var f = document.body.appendChild(JsElement('form')); // IE
				data = data.concat(toQueryArray(options.data));
			}

			data.forEach(function (d) {
				f.appendChild(JsElement('input', {
					type: 'hidden',
					name: d[0],
					value: d[1],
				}));
			});

			f.action = options.url;
			f.method = options.method;
			f.submit();

			if (typeof options.onSuccess == 'function') {
				options.onSuccess();
			}
		},

		ajax: function (options, forceOptions) {
			if (!online) {
				if (!offline_displayed) {
					offline_displayed = true;
					offlineToast();
				}
			}

			options = Object.assign({}, options, forceOptions);

			if (options.update) {
				var el = options.update;
				delete options.update;
				options.onSuccess = function (html) {
					if (typeof el == 'string') {
						el = document.querySelector(el);
					}
					el.innerHTML = html;
				};
			}

			// data
			let data = getType(options.data) == 'array' ? options.data : [options.data];
			let fetchOptions = {};

			// spinner
			if (options.spinner !== false) {
				if (typeof spinner != 'function') {
					options.spinner = JsLoading;
				}
				options.spinner(true);
			}

			if (typeof options.format != 'undefined') {
				options.headers ??= {};
				if (options.responseType == 'json') {
					options.headers['Accept'] = 'application/json';
				}
				//options.headers['X-Respond-As'] = options.format;
				options.url = toURLGet(options.url, { format: options.format });
			}

			if (options.method == 'GET') {
				options.url = toURLGet(options.url, data);
			} else {
				if (options.method && options.method != 'POST') {
					data.push({ __method: options.method.toUpperCase() });
				}

				fetchOptions.method = 'POST';
				fetchOptions.body = toFormData(data);
			}

			['cache', 'credentials', 'headers', 'integrity', 'mode', 'redirect', 'referrer', 'referrerPolicy']
				.forEach(function (prop) {
					if (typeof options[prop] != 'undefined') {
						fetchOptions[prop] = options[prop];
					}
				});

			fetch(options.url, fetchOptions)
				.then(function (response) {
					if (options.spinner) {
						options.spinner(false);
					}
					/* if (!response.ok) {
						throw new Error(response.statusText);
					} */

					return response[options.responseType]();
				})
				.then(function (data) { // filters
					switch (options.responseType) {
						case 'json':
							if (data.__error) {
								throw new Error(data.__error);
							}
							if (data.__alert) {
								Modal(data.__alert);
							}
							if (data.__profiler) {
								JsConsole.log(data.__profiler);
							}
							break;

						case 'text':
							let log = JsConsole.getTextData(data);
							if (log) {
								JsConsole.log(log);
							}
							break;
					}

					return data;
				})
				.then(function (data) {
					if (typeof options.onSuccess == 'function') {
						options.onSuccess.call(options, data);
					}
				})
				.catch(function (e) {
					JsToast({
						message: `${e.name}: ${e.message}.`,
						type: 'danger'
					});
				});
		},
	};
})();

/*
 * Implement basic methods
 */
JsRequest.implement({
	blank: function (options) {
		return this.link(options, {
			target: '_blank'
		});
	},

	get: function (options) {
		return this.http(options, {
			method: 'GET'
		});
	},

	post: function (options) {
		return this.http(options, {
			method: 'POST'
		});
	},

	text: function (options) {
		return this.ajax(options, {
			format: 'text',
			responseType: 'text',
		});
	},

	json: function (options) {
		return this.ajax(options, {
			format: 'json',
			responseType: 'json',
			update: false,
		});
	},
});