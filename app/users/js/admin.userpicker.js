
/**
 * Userpicker
 */
JsFelem.implement({
	userpicker: function (el) {
		JsCollection(el, {
			url: JsUrl('admin/users/users'),
		});
	}
});
