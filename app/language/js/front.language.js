
/* --- Language --------------------------------- */
window.addEventListener('load', function () {
	let f = JsForm('#language-form').request(JsUrl('/language/change'), function (message, code) {
		if (code) {
			window.location.reload();
		} else {
			alert(message);
		}
	});

	f.getForm().lang.addEventListener('change', function () {
		f.submit();
	});
});