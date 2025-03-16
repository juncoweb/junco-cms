
/* --- Password ------------------------------------------------ */
JsFelem.implement({
	password: function(el, box) {
		if (el.tagName == 'INPUT') {
			let btn = JsElement('div.btn', {
				html: '<i class="fa-solid fa-eye"></i>',
				events: {
					click: function() {
						let status = el.type == 'password';
						el.type = status ? 'text' : 'password';
						btn.firstChild.className = status ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye';
					}
				}
			});
			let group = el.parentNode.insertBefore(JsElement('div.input-group'), el);
			group.appendChild(el)
			group.appendChild(btn);
		}
	}
});