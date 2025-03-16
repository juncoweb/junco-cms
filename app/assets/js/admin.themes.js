
/* --- Themes ------------------------------------------- */
let AssetsThemes = (function () {
	function $U(task) {
		return JsUrl('admin/assets.themes/' + task);
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
	let _controls = {
		create: {
			modalOptions: {
				//size: 'large',
				onLoad: function () {
					target = this;
					JsForm({ btn: this }).request($U('save'), callback);
				},
			},
		},
		copy: {
			numRows: '1',
			modalOptions: {
				//size: 'large',
				onLoad: function () {
					target = this;
					JsForm({ btn: this }).request($U('save'), callback);
				},
			},
		},
		confirm_delete: {
			numRows: '1',
			modalOptions: {
				onLoad: function () {
					target = this;
					JsForm({ btn: this }).request($U('delete'), callback);
				},
			},
		},
		confirm_compile: {
			numRows: '1',
			modalOptions: {
				onLoad: function () {
					target = this;
					JsForm({ btn: this }).request($U('compile'), callback);
				},
			},
		},
		confirm_select: {
			numRows: '1',
			modalOptions: {
				onLoad: function () {
					target = this;
					JsForm({ btn: this }).request($U('select'), callback);
				},
			},
		},
	};

	return {
		List: function () {
			_backlist = Backlist()
				.url($U)
				.controls(_controls)
				.allowHistory()
				.load();
		},
		setControls: function (controls) {
			for (let i in controls) {
				_controls[i] = controls[i];
			}
		},
	};
})();
