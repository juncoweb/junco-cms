
/* --- Jobs ------------------------------------------ */
let Jobs = (function () {
	function $U(task) {
		return JsUrl('admin/jobs/' + task);
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

	let _backlist, target;
	let mo = {
		size: 'large',
		onLoad: function () {
			target = this;
			JsForm().request($U('save'), callback);
		},
	};
	let _controls = {
		show: {
			numRows: '1',
			modalOptions: {
				size: 'large',
				onLoad: function () {

				},
			},
		},

		confirm_delete: {
			modalOptions: {
				onLoad: function () {
					target = this;
					JsForm({ btn: this }).request($U('delete'), callback);
				},
			},
		},
	};

	return {
		List: function () {
			if (!_backlist) {
				_backlist = Backlist()
					.url($U)
					.controls(_controls)
					.allowHistory()
					.load();
			}
			return _backlist;
		},
		setControls: function (controls) {
			for (let i in controls) {
				_controls[i] = controls[i];
			}
		},
	};
})();
