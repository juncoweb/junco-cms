
/* --- Variables ------------------------------------------- */
var AssetsVariables = (function () {
	function $U(task) {
		return JsUrl('admin/assets.variables/' + task);
	}

	function callback(message, code) {
		if (code) {
			if (target) {
				target = target.close();
			}
			_backlist.refresh();
		}
		(target || _backlist).notify(message);
	}

	var target;
	var _controls = {
		edit: {
			numRows: '1',
			modalOptions: {
				size: 'large',
				onLoad: function () {
					target = this;
					JsForm({ btn: this }).request($U('update'), callback);
				},
			},
		},
	};

	return {
		List: function (key) {
			_backlist = Backlist()
				.url($U)
				.controls(_controls)
				.data({ key: key })
				.allowHistory()
				.load();
		},
		setControls: function (controls) {
			for (var i in controls) {
				_controls[i] = controls[i];
			}
		},
	};
})();
