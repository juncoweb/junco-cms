/**
 * Lightbox
 *
 * @return
 * the element box
*/

function Lightbox(force, options) {
	let overlay = document.body.appendChild(JsElement('div.lightbox', {
		html: '<div></div><i class="fa-solid fa-xmark lightbox-cross"></i>'
	}));
	let el = overlay.firstChild;
	options = Object.assign({
		hideOnlyWithButton: false,
		valignCenter: true,
	}, options);

	function cross(btn, box) {
		btn.addEventListener('click', function () {
			el.hide();
		});
		box.addEventListener('click', function (event) {
			if (event.target !== box) {
				event.stopPropagation();
			}
		});
	}

	if (options.valignCenter) {
		overlay.classList.add('valign-center');
	}
	if (options.hideOnlyWithButton) {
		cross(overlay.querySelector('.lightbox-cross'), overlay);
	} else {
		cross(overlay, el);
	}

	el.destroy = function () {
		overlay.parentNode.removeChild(overlay);
		document.body.classList.remove('lightbox-fixed');
	};
	el.toggle = function (force) {
		overlay.style.display = document.body.classList.toggle('lightbox-fixed', force) ? '' : 'none';
	};
	el.show = function () {
		this.toggle(true);
	};
	el.hide = function () {
		this.toggle(false);
	};
	el.toggle(force);

	return el;
};
