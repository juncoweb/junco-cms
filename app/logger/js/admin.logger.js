
/* --- Logger ----------------------------------------- */
let Logger = (function () {
	function $U(task) {
		return JsUrl('admin/logger/' + task);
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
			let _backlist = Backlist()
				.url($U)
				.controls({
					show: {
						numRows: '1',
						modalOptions: {
							size: 'large',
							onLoad: function () {
								target = this;
							},
						},
					},

					status: {
						onSuccess: callback,
					},

					confirm_delete: {
						modalOptions: {
							onLoad: function () {
								target = this;
								JsForm({ btn: this }).request($U('delete'), callback);
							},
						},
					},

					confirm_thin: {
						numRows: '*',
						modalOptions: {
							onLoad: function () {
								target = this;
								JsForm({ btn: this }).request($U('thin'), callback);
							},
						},
					},

					confirm_clean: {
						numRows: '*',
						modalOptions: {
							onLoad: function () {
								target = this;
								JsForm({ btn: this }).request($U('clean'), callback);
							},
						},
					},

					confirm_report: {
						numRows: '*',
						modalOptions: {
							onLoad: function () {
								target = this;
								JsForm({ btn: this }).request($U('report'), callback);
							},
						},
					},
				})
				.allowHistory()
				.load();
		},
	};
})();
