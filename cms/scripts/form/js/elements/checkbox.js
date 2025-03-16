
/* --- Checkbox ----------------------------------------------------------- */
JsFelem.implement({
	'check-all': function(checkall, form) {
		const name = checkall.getAttribute('data-checkall');
		const elements = form.querySelectorAll('[name="'+ name +'[]"]');

		function areAllChecked() {
			let i = elements.length
			while (i--) {
				if (!elements[i].checked) {
					return false;
				}
			}
			return true;
		}
		function fn() {
			if (checkall.checked != areAllChecked()) {
				checkall.locked = true;
				checkall.parentNode.click();
			}
		}

		checkall.addEventListener('change', function() { 
			if (checkall.locked) {
				checkall.locked = false;
			} else {
				elements.forEach(function(el) {
					if (el.checked != checkall.checked) {
						el.locked = true;
						el.parentNode.click();
					}
				});
			}
		});
		elements.forEach(function(el) {
			el.addEventListener('change', function() {
				if (el.locked) {
					el.locked = false;
				} else {
					fn();
				}
			});
		});
		fn();
	}
});
