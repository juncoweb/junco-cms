/* --- Console ----------------------------------------------------- */
var ConsoleFrame = {
	init: function (_window) {
		document.querySelector('console h1 .toggle').addEventListener('click', function () {
			_window.toggle();
		});
	},

	log: function (report) {
		var _content = document.querySelector('console > div');
		_content.innerHTML = report + _content.innerHTML;
	},
};

//window.addEventListener('load', function() { alert(); });