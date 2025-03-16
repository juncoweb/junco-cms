/* --- active header ---------------------------- */
var ActiveHeader = function (el, css, top) {
	if (typeof el == 'string') {
		el = document.querySelector(el);
	}
	css = css || 'active';
	top = top || 24;
	var status = false;

	function scrollTop() {
		return window.pageYOffset || document.documentElement.scrollTop;
	}
	function fn() {
		if (status != (scrollTop() > top)) {
			status = el.classList.toggle(css);
		}
	}

	fn();
	window.addEventListener('scroll', fn);
};
