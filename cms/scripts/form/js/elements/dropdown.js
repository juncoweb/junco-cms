
/* --- Dropdown and Select control ------------------------------------------------ */
(function () {
	function dropdown(el) {
		let isSelect = el.getAttribute('control-felem') == 'select';
		let hasSubmit = el.getAttribute('data-on-change') == 'submit';
		let isSimple = el.className != 'btn-group';
		let box = isSimple ? el.parentNode : el;
		let btn = isSimple ? el : (el.querySelector('[role=caret]') || el.querySelector('.dropdown-toggle'));
		let menu = (box.querySelector('[role=drop-menu]') || box.querySelector('.dropdown-menu'));

		function fn(event) {
			event.stopPropagation();
			JsDropdown(menu, {
				onToggle: function (status) {
					el.classList.toggle('active', status);
					btn.setAttribute('aria-expanded', status);
					if (status) {
						let rect = menu.getBoundingClientRect();

						if (rect.right > window.innerWidth) {
							menu.style.right = '-1px';
							menu.style.left = 'auto';
						} else if (rect.left < 0) {
							menu.style.right = 'auto';
							menu.style.left = '0px';
						}
					}
				},
			}).toggle();
		}

		// drop
		btn.addEventListener('click', fn);
		btn.setAttribute('aria-expanded', false);

		if (isSimple) {
			menu.style.left = (el.getBoundingClientRect().left - el.parentNode.getBoundingClientRect().left) + 'px';
		} else {
			menu.style.right = '-1px';
		}

		// change
		if (isSelect) {
			let label = isSimple ? el : el.querySelector('[data-select-label]');

			if (label) {
				let hidden = menu.querySelector('input[type=hidden]');
				let all = Array.from(menu.getElementsByTagName('LI'));

				all.forEach(function (el) {
					el.addEventListener('click', function (event) {
						event.stopPropagation();

						all.forEach(function (el2) {
							el2.className = (el2 == el ? 'selected' : '');
						});

						JsDropdown.hide();
						label.innerHTML = el.textContent;
						hidden.value = el.getAttribute('data-select-value');

						if (hasSubmit) {
							JsFelem.submit(hidden.form);
						}
					});
				});
			}
		}
	};

	// felem implement
	JsFelem.implement({
		'select': dropdown,
		'dropdown': dropdown
	});
})();