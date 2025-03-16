
/* --- Usys ------------------------------------------------------ */
function UsysAccount() {
	var $form = JsForm().request(JsUrl('/usys.account/update'), function (message, code) {
		$form.notify(message);
	});
}