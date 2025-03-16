
/* --- Press ------------------------------------------------ */
JsFelem.implement({
	'press': function (el, box) {
		let input = el.querySelector('input');

		if (input) {
			let all = (input.type == 'radio')
				? Array.from(box.querySelectorAll('input[type=radio][name=' + input.name + ']'))
				: [input];

			input.addEventListener('change', function () {
				all.forEach(function (el) {
					el.parentNode.classList[el.checked ? 'add' : 'remove']('checked');
				});
			});
		}
	}
});