
/**
 * Form Elements object
 */
const JsFelem = (function () {
	const _observe = {};
	const _felem = {
		submit: function (el) {
			el.addEventListener(el.getAttribute('data-value') || 'click', function () {
				JsFelem.submit(el);
			});
		}
	};

	const that = {
		/**
		* Implement
		*
		* @ Add handle function
		* 
		* @param handles (object) - {handle:fn[, ..]}
		*/
		implement: function (handles) {
			for (let handle in handles) {
				if (typeof handles[handle] == 'function') {
					_felem[handle] = handles[handle];
				}
			}
		},

		/**
		* Observe
		*
		* @ Add functions without the need of the handler.
		* 
		* @param handles (object) - {selector:fn[, ..]}
		*/
		observe: function (handles) {
			for (let handle in handles) {
				if (typeof handles[handle] == 'function') {
					_observe[handle] = handles[handle];
				}
			}
		},

		/**
		* Load
		*
		* @ Load functions
		* 
		* @param box (element) - Element where functions will be loaded
		* @param data (Array) - Array of data, see JsForm
		*/
		load: function (box, data) {
			if (!box) {
				box = document;
			} else if (typeof box === 'string') {
				box = document.querySelector(box);
			}

			// load
			Array.from(box.querySelectorAll('*[control-felem]')).forEach(function (el) {
				el.getAttribute('control-felem').split(' ').forEach(function (handle) {
					if (handle && typeof _felem[handle] == 'function') {
						_felem[handle](el, box, data);
					}
				});
			});

			// observe
			function observeFn(all, fn) {
				all.forEach(function (el) {
					fn(el, box, data);
				});
			}

			for (let i in _observe) {
				observeFn(Array.from(box.querySelectorAll(i)), _observe[i]);
			}
		},

		/**
		* Submit
		*
		* @ Try to submit the form
		* 
		* @param form (element) - Form or form element
		*/
		submit: function (form) {
			for (; form.tagName != 'FORM'; form = form.parentNode) {
				if (form.tagName == 'BODY') {
					return;
				}
			}

			let btn = form.querySelector('*[type="submit"]');
			if (btn) {
				btn.click();
			} else {
				btn = form.appendChild(document.createElement('input'));
				btn.type = 'submit';
				btn.style.display = 'none';
				btn.click();
				form.removeChild(btn);
			}
		},
	};

	return that;
})();
