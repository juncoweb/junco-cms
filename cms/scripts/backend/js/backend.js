
/* --- Backend ------------------------------------------ */
var Backend = (function () {
	const controls = JsControls({ tpl: {} });

	return {
		attach: function (name, fn) {
			controls.attach('tpl', name, fn);
		},

		attachAll: function (obj) {
			controls.attachAll('tpl', obj);
		},

		load: function (box) {
			controls.load('tpl', box);
		}
	};
})();

window.addEventListener('DOMContentLoaded', function () {
	const el = Navbar('.navbar', '.pull-btn');
	if (el) {
		el.minimizer('.navbar-minimizer > a');
	}
	const h = document.body.querySelector('.layout-header');
	if (h) {
		JsFelem.load(h);
	}
	Backend.load(document);
});