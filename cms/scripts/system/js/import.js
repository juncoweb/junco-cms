/**
 * Js import
 *
 * @params:
 * resources - (string|array) resource list, js or css, in array or separated by commas
 * 
 * return Promise
 */
let JsImport = (function () {
	let promises = {};
	let head = document.querySelector('head');

	function i(resources) {
		if (typeof resources === 'string') {
			return resources.split(',').map(value => value.trim());
		}

		return resources ? Array.isArray(resources) : [];
	}

	function getExtension(f) {
		return f.substr(f.lastIndexOf('.') + 1);
	}

	function getElementPromise(tagName, attr, resource) {
		return new Promise((resolve, reject) => {
			let el = head.appendChild(document.createElement(tagName));

			el.onload = function () {
				promises[resource].complete = true;
				resolve();
			};

			el.onerror = function () {
				reject();
			};

			for (let attrName in attr) {
				el[attrName] = attr[attrName];
			}
		});
	}

	return function (resources) {
		let All = [];

		i(resources).forEach(function (resource) {
			if (typeof promises[resource] == 'object') {
				All.push(promises[resource].complete ? Promise.resolve() : promises[resource].promise);
			} else {
				const extension = getExtension(resource);

				if (['js', 'css'].indexOf(extension) == -1) {
					return;
				}

				const promise = (extension === 'js')
					? getElementPromise('script', { async: true, src: resource }, resource)
					: getElementPromise('link', { type: 'text/css', rel: 'stylesheet', href: resource }, resource);

				All.push(promise);
				promises[resource] = {
					complete: false,
					promise: promise
				};
			}
		});

		return Promise.all(All);
	};
})();
