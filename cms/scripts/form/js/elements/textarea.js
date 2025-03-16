
/* --- Textarea ---------------------------------------------------- */
var FeTextarea = {
	/**
	 * Max Chars
	 * @Texarea max chars handle
	 */
	'max-chars': function (el) {
		var max = el.getAttribute('data-max-chars');
		var div = el.parentNode.insertBefore(JsElement('div.color-light'), el.nextSibling);
		var fn = function (event) {
			var total = max - el.value.length;
			if (total < 0) {
				event.preventDefault();

				el.value = el.value.substring(0, max);
				total = '<span class="color-danger">0</span>';
			}
			div.innerHTML = total;
		};

		if (max) {
			el.addEventListener('keydown', fn);
			if ('oninput' in el) {
				el.addEventListener('input', fn);
			}
			fn();
		}
	},

	/**
	 * Auto Grow
	 * @Texarea auto grow handle
	 */
	'auto-grow': function (el) {
		if ('field-sizing' in document.body.style) {
			return;
		}

		const maxHeight = parseInt(el.getAttribute('data-max-height'));
		const minHeight = parseInt(el.getAttribute('data-min-height') || window.getComputedStyle(el).getPropertyValue('height')) || 30;

		function fn() {
			el.style.height = '0'; // Reset the height
			el.style.height = Math.max(parseInt(maxHeight ? Math.min(el.scrollHeight, maxHeight) : el.scrollHeight), minHeight) + 'px';
		}

		el.style.overflow = 'hidden';
		el.style.wordWrap = 'break-word';
		el.style.resize = 'none';
		el.addEventListener('oninput' in el ? 'input' : 'keyup', fn);
		el.autoGrow = fn;
		fn();
	},
};

JsFelem.implement(FeTextarea);
