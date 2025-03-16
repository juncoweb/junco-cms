
/* --- UsersRoles ----------------------------------- */
var UsersRoles = (function () {
	function $U(task, output) {
		return JsUrl('admin/users.roles/' + task, false, output);
	}

	return {
		List: function () {
			function callback(message, code) {
				if (code) {
					if (target) {
						target = target.close();
					}
					_backlist.refresh();
				}
				(target || _backlist).notify(message);
			}

			let target;
			let mo = {
				onLoad: function () {
					target = this;
					JsForm({ btn: this }).request($U('save'), callback);
				},
			};

			let _backlist = Backlist()
				.url($U)
				.controls({
					create: {
						modalOptions: mo,
					},
					edit: {
						numRows: 1,
						modalOptions: mo,
					},
					confirm_delete: {
						modalOptions: {
							onLoad: function () {
								target = this;
								JsForm({ btn: this }).request($U('delete'), callback);
							},
						},
					},
				})
				.allowHistory()
				.load();
		},
	};
})();
