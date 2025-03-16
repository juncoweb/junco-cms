
/* --- Permissions ------------------------------------ */
var Permissions = (function () {
	function $U(task) {
		return JsUrl('admin/users.permissions/' + task);
	}

	return {
		List: function () {
			function callback(message, code) {
				if (code) {
					_backlist.refresh();
				}
				_backlist.notify(message);
			};
			let _backlist = Backlist()
				.url($U)
				.controls({
					status: {
						onSuccess: callback,
					},
				})
				.allowHistory()
				.load();
		},
	};
})();
