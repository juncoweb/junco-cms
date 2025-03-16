
/* --- test ----------------------------------------- */
function load() {
	var _Form = JsForm().request(JsUrl('admin/sa/request'), function(message) {
		_Form.notify(message)
	});
}
