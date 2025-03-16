/**
 * JsControls
 * @create page events from «control-handle» attribute.
 * 
 * @controls - (object) initialize object. Model {handle: { name1: function(){}, name2: function(){} }}
 */

var JsControls = function (controls) {
	controls = Object.assign({}, controls);

	return {
		/**
		 * attach
		 * @attach one page events
		 */
		attach: function (handle, name, fn) {
			controls[handle][name] = fn;
			return this;
		},

		attachAll: function (handle, obj) {
			if (typeof controls[handle] != 'object') {
				controls[handle] = {};
			}

			for (let name in obj) {
				controls[handle][name] = obj[name];
			}
			return this;
		},

		/**
		 * load
		 * @create the page events
		 */
		load: function (handle, box, fn) {
			let attr = 'control-' + handle;

			if (typeof fn != 'function') {
				fn = function (el, fn, name) {
					fn(el, name);
				};
			}

			Array.from(box.querySelectorAll('[' + attr + ']'))
				.forEach(function (el) {
					let name = el.getAttribute(attr) || 'default';
					if (controls[handle][name]) {
						fn(el, controls[handle][name], name);
					}
				});

			return this;
		},
	};
};