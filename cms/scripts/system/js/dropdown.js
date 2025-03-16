/**
 * Drop down
 *
 * @author: Junco CMS (tm)
 * @events:
 * onShow
 * onHide
 * onToggle
 *
 */

const JsDropdown = (function() {
	let current;
	let that;

	return function(el, options) {
		if (typeof el === 'string') {
			el = document.querySelector(el);
		}

		if (!el) {
			if (that) {
				that.hide();
			}
			return;
		}

		if (el == current) {
			return that;
		} else if (that) {
			that.hide();
		}

		options = Object.assign({
			onToggle: null,
			onShow: null,
			onHide: null
		}, options);
		current = el;
		that = {
			show: function() {
				that.toggle(true);
			},

			hide: function() {
				that.toggle(false);
			},

			toggle: function(status) {
				if (!current) {
					return;
				}
				if (typeof status == 'undefined') {
					status = window.getComputedStyle(current)['display'] == 'none';
				}

				function fn(evName, displayValue, fireEvents) {
					document[evName]('click', that.hide);
					current[evName]('click', function(event) {
						event.stopPropagation(); 
					});
					current.style.display = displayValue;
					fireEvents.forEach(function(fireEvent) {
						if (typeof options[fireEvent] == 'function') {
							options[fireEvent](status);
						}
					});
				}

				if (status) {
					fn('addEventListener', '', ['onToggle', 'onShow']);
				} else {
					fn('removeEventListener', 'none', ['onToggle', 'onHide']);
					current = that = null;
				}
			}
		};

		return that;
	};
})();

JsDropdown.hide = function() {
	JsDropdown(false);
};

