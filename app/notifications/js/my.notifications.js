
/* --- Notifications ------------------------------------------ */
let MyNotifications = (function () {
	function $U(task) {
		return JsUrl('my/notifications/' + task);
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
		status: {
			onSuccess: callback,
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
