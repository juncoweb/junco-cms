
/* --- Web Store ------------------------------------------------- */
let Webstore = (function () {
	function $U(task) {
		return JsUrl('admin/extensions.webstore/' + task);
	}

	let _backlist, target;
	let _controls = {
		confirm_download: {
			modalOptions: {
				//size: 'medium',
				onLoad: function () {
					target = this;
					JsForm({ btn: this }).request($U('download'), function (message, code) {
						if (code) {
							if (target) {
								target = target.close();
							}
							_backlist.refresh();
						}
						(target || _backlist).notify(message);
					});
				}
			}
		},
	};

	return {
		List: function () {
			if (!_backlist) {
				_backlist = Backlist()
					.url($U)
					.controls(_controls)
					.allowHistory()
					.load({
						onSuccess: function () {
							JsRating();
						}
					});
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
