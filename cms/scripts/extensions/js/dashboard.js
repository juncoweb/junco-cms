
window.addEventListener('load', function () {
	let el = document.querySelector('#extensions-updates');
	JsRequest.xjs({
		url: JsUrl('admin/extensions.installer/find_updates'),
		data: el.querySelector('form'),
		onSuccess: function (html, code) {
			if (el && code && html != 'null') {
				el.innerHTML = html;
			} else {
				el.parentNode.removeChild(el);
			}
		},
	});
});

