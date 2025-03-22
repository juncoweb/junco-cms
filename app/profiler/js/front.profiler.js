/* --- Console ----------------------------------------------------- */
var JsConsole = (function () {
	var counter = 0;

	function now() {
		var date = new Date();

		date = [date.getHours(), date.getMinutes(), date.getSeconds()];
		for (var i in [0, 1, 2]) {
			if (date[i] < 10) {
				date[i] = '0' + date[i];
			}
		}

		return date.join(':');
	}

	return {
		clear: function () {
			counter = 0;
			document.querySelector('console > div').innerHTML = '';
		},

		log: function (report) {
			var content = document.querySelector('console > div');

			content.innerHTML = '<fieldset>' +
				'<legend><i>' + (++counter) + '</i> ' + report.title + '<span>' + now() + '</span></legend>' +
				report.content +
				'</fieldset>' +
				content.innerHTML;
		},
	};
})();

window.addEventListener('load', function () {
	var _window = window.opener || document.querySelector('iframe').contentWindow;

	if (window.opener) {
		_window.JsConsole.getTails().forEach(function (report) {
			JsConsole.log(report);
		});
	}

	document.querySelector('console h1 .clear').addEventListener('click', function () {
		JsConsole.clear();
	});
	document.querySelector('console h1 .toggle').addEventListener('click', function () {
		_window.JsConsole.toggle();
	});
});