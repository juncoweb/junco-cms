
/* --- ToPrint -------------------------------------------- */
function ToPrint(el) {
	let html = '';
	switch (typeof el) {
		case 'string': el = document.querySelector(el);
		case 'object': html = el.innerHTML;
			break;
		case 'undefined': html = document.body.innerHTML;
			break;
	}

	html = '<html>\n<head>\n'
		+ document.querySelector('head').innerHTML
		+ '\n</he' + 'ad>\n<body>\n'
		+ (html || 'Error! No contents.')
		+ '\n</bo' + 'dy>\n</ht' + 'ml>';

	let frm = document.createElement('iframe');
	frm.name = 'print-frame';
	frm.style.position = 'absolute';
	frm.style.top = '-100000px';
	//document.body.insertBefore(frm, document.body.firstChild);
	document.body.appendChild(frm);

	let doc = (frm.contentWindow || frm.contentDocument);
	if (doc.document) {
		doc = doc.document;
	}
	doc.open();
	doc.write(html);
	doc.close();

	setTimeout(function () {
		let f = window.frames[frm.name];
		f.focus();
		f.print();
		document.body.removeChild(frm);
	}, 500);
};
