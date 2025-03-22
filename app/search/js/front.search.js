
/* --- Search ---------------------------------------------------------------------- */
var Search = (function () {
	var f = document.getElementById('search-form'),
		engines = f.querySelectorAll('input[type=radio]');

	if (engines) {
		for (var i = 0, L = engines.length; i < L; i++) {
			engines[i].addEventListener('change', function () { f.querySelector('button[type=submit]').click() });
		}
	}
})();
