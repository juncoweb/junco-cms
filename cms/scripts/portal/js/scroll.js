var JsScroll = (function () {
	let events = {};

	function visible(el) {
		let viewTop = window.pageYOffset;
		let viewBottom = viewTop + window.innerHeight;
		let rect = el.getBoundingClientRect();
		let _top = viewTop + rect.y;
		let _bottom = _top + rect.height;

		return (_top <= viewBottom) && (_bottom >= viewTop);
	}

	function callback(el, fire) {
		el.classList.add('active');

		if (fire) {
			return events[fire].call(el);
		}

		return true;
	}

	function getEvent(el) {
		for (let fire in events) {
			if (el.classList.contains(fire)) {
				return fire;
			}
		}
	}

	return {
		addEvent: function (name, fn) {
			if (typeof fn == 'function') {
				events[name] = fn;
			}
		},
		start: function () {
			let all = document.querySelectorAll('.on-scroll');
			if (all) {
				all.forEach(function (el) {
					let fire = getEvent(el);
					function fn() {
						if (visible(el)) {
							if (callback(el, fire)) {
								window.removeEventListener('scroll', fn);
							}
						}
					}
					window.addEventListener('scroll', fn);
					fn();
				});
			}
		}
	};
})();

window.addEventListener('load', function () {
	JsScroll.start();
});