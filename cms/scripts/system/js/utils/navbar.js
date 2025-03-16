/* --- nav -------------------------------------------- */
const Navbar = function (nav, btn, options) {
	if (typeof nav == 'string') {
		nav = document.querySelector(nav);
	}
	if (typeof btn == 'string') {
		btn = document.querySelector(btn);
	}
	if (!nav || !btn) {
		return;
	}

	nav.toggle = function (value) {
		if (value) {
			this.classList.replace('navbar-mobile', 'navbar');
		} else {
			this.classList.replace('navbar', 'navbar-mobile');
		}
	};

	// vars
	options = JsMergeOptions({
		//onSelect
		//onChange
	}, options);

	let curDisplay, displayValue, _nav;
	let parent = nav.parentNode;
	let sibling = nav.nextSibling;
	let body = document.body;
	let isOpen = 0;

	// Accordion
	let hasAccordion = nav.classList.contains('navbar-control');
	function Accordion() {
		let li = nav.querySelectorAll(':scope > ul > li');

		li.forEach(function (el, i) {
			if (el.querySelector('ul')) {
				let a = el.querySelector('a');
				a.href = 'javascript:void(0)';
				a.setAttribute('aria-expanded', el.classList.contains('expand'));
				a.addEventListener('keydown', function (event) {
					if (event.key == 'Enter') {
						minimizerFn(false);
					}
				});
				a.addEventListener('click', function () {
					if (options.fire('select', el) === false) {
						return;
					}
					li.forEach(function (el, j) {
						let expanded = el.classList.contains('expand');
						let status = (i == j && !expanded);

						if (expanded) {
							if (!status) {
								el.classList.remove('expand');
							}
						} else if (status) {
							el.classList.add('expand');
						}
						el.querySelector('a').setAttribute('aria-expanded', status);
						options.fire('change', el, status);
					});
				});
			}
		});
	}

	// main
	let isInitialized = false;
	function initialize() {
		_nav = body.appendChild(JsElement('div.pull-navbar'));

		// touch event
		let _start, corrector;
		let _overlay = body.appendChild(JsElement('div.pull-overlay'));
		let _target = body.appendChild(JsElement('div.pull-target', {
			events: {
				click: function () {
					if (isOpen) {
						_toggle();
					}
				}
			}
		}));
		function Escape(event) {
			if (event.key == 'Escape') {
				btn.click();
			}
		}
		function _toggle(v) {
			_nav.click();
			isOpen = body.classList.toggle('pull-on', v instanceof Event ? undefined : v);
			if (isOpen) {
				let a = _nav.querySelector('a');
				if (a) {
					a.focus();
				}
				document.addEventListener('keydown', Escape);
			} else {
				btn.focus();
				document.removeEventListener('keydown', Escape);
			}
		}

		function _move(v) {
			_nav.style.transition = ['', 'initial'][v];
			_overlay.style.display = ['', 'block'][v];
		}

		JsMove(_target,
			// start
			function (event) {
				let rect = event.target.getBoundingClientRect();
				_start = {
					x: event.clientX,
					left: rect.left,
					right: (rect.left + rect.width),
					width: rect.width
				},
					corrector = _nav.getBoundingClientRect().width;
				_move(1);
			},
			// move
			function (event) {
				let x = event.clientX, v = (_start.x - x);
				if (v < 0) { // to right
					if (x < _start.right || x > corrector) return;
					x -= _start.width;
				} else if (v > 0) { // to left
					if (x > _start.left) return;
				} else {
					return;
				}

				_target.style.left = x + 'px';
				_nav.style.transform = 'translateX(' + (x - corrector) + 'px)';
			},
			// end
			function (event) {
				_target.style.left = '';
				_nav.style.transform = '';

				_move(0);
				_toggle((Math.abs(_start.left - event.clientX) > corrector / 2) ? !isOpen : isOpen);
			}
		);

		// btn
		if (btn) {
			btn.addEventListener('click', _toggle);
		}

		return true;
	}

	function fn() {
		displayValue = window.getComputedStyle(btn).getPropertyValue('display');

		if (displayValue !== curDisplay) {
			if (displayValue === 'none') {
				if (isInitialized) {
					parent.insertBefore(nav, sibling);
				}
				nav.toggle(1);
			} else {
				if (!isInitialized) {
					isInitialized = initialize();
				}
				nav.toggle(0);
				_nav.appendChild(nav);
			}
			if (hasAccordion) {
				hasAccordion = false;
				Accordion();
			}
			curDisplay = displayValue;

			body.classList.toggle('body-small', curDisplay !== 'none');
		}
	}

	let hash = nav.getAttribute('data-hash');
	if (hash) {
		let el = nav.querySelector('[data-hash="' + hash + '"]');
		if (el) {
			while (true) {
				el = el.parentNode;
				if (el.tagName == 'LI') {
					el.classList.add('selected');
				} else if (el == nav) {
					break;
				}
			}
		}
	}

	fn();
	window.addEventListener('resize', fn);

	let minimizer;
	function minimizerFn(force) {
		if (force === undefined) {
			force = JsCookie.get('BackendNavbar');
		} else {
			setCookie(force);
		}

		body.classList.toggle('navbar-minimized', force);
	}

	function setCookie(v) {
		JsCookie.set('BackendNavbar', v ? 1 : '');
	}

	return {
		minimizer: function (slc) {
			minimizer = document.querySelector(slc);
			minimizer.addEventListener('click', function () {
				setCookie(!body.classList.contains('navbar-minimized'));
				minimizerFn();
			});
			minimizerFn();
		}
	}
};
