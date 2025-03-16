
/* --- Test ----------------------------- */
var AdTest = {
	load: function () {
		/*JsFelem.implement({
			'test': function(el) {
				JsCollection(el, {
					url: JsUrl('admin/usys.users/json'),
				});
			},
		});
		*/
		JsCollection(document.querySelectorAll('[control-felem=test]'), {
			justUse: 1,
			url: JsUrl('admin/usys.users/json'),
		});
		JsCollection(document.querySelectorAll('[control-felem=userpicker]'), {
			justUse: 1,
			url: JsUrl('admin/usys.users/json'),
		});
		JsCollection(document.querySelectorAll('[control-felem=xuserpicker]'), {
			justUse: 1,
			url: JsUrl('admin/usys.users/json'),
		});
		//JsFelem.load(document);
		//JsForm().request();
	}
};
