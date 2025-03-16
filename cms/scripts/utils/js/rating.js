/* --- Rating ---------------------------------------- */
function JsRating(box, options) {
	function getElements(box) {
		if (typeof box == 'string') {
			box = document.querySelector(box);
		}
		if (box && box.getAttribute('data-rating')) {
			return [box];
		}

		return (box || document).querySelectorAll('*[data-rating]') || [];
	}

	function getValue(el) {
		let value = el.getAttribute('data-rating');
		if (value.includes('|')) {
			value = getAverage(value);
		}
		if (parseInt(value) == parseFloat(value)) {
			return parseInt(value);
		}
		return parseFloat(value).toFixed(1);
	}

	function getAverage(value) {
		let t = p = 0;
		let sum = value.split('|').reduce(function (a, n) {
			n = parseInt(n);
			t += n;
			return a += (++p) * n;
		}, 0);

		return t ? (sum / t) : 0;
	}

	function setStars(stars, value) {
		let half = parseInt(value) !== value;
		let css = '';
		value -= 1;

		for (let i = 0; i < 5; i++) {
			if (i <= value) {
				css = 'fa-solid fa-star';
			} else if (half) {
				half = false;
				css = 'fa-solid fa-star-half-stroke';
			} else {
				css = 'fa-regular fa-star';
			}
			stars[i].className = css;
		}
	}

	// options
	if (typeof box == 'object' && typeof box.tagName == 'undefined') {
		options = box;
		box = undefined;
	}

	options = Object.assign({
		type: 'default',
		span: undefined,
		onLoad: undefined,
		onSelect: undefined,
		onToggle: undefined
	}, options);

	// box
	let elements = getElements(box);

	elements.forEach(function (el) {
		el.innerHTML = '<i></i>'.repeat(5) + el.innerHTML;

		let value = getValue(el);
		let stars = el.querySelectorAll('i');

		if (options.type == 'default') {
			el.appendChild(JsElement('span', { html: '(' + value + ')' }));
		} else if (options.span) {
			options.span.innerHTML = value;
		}

		setStars(stars, value);

		if (options.onSelect) {
			el.setAttribute('tabindex', 0);
			el.setAttribute('role', 'radiogroup');
			//el.addEventListener();
			stars.forEach(function (star, i) {
				star.value = i + 1;
				star.addEventListener('mouseover', function () {
					setStars(stars, star.value);
					if (options.onToggle) {
						options.onToggle.call(star, true)
					}
				});
				star.addEventListener('mouseout', function () {
					setStars(stars, value);
					if (options.onToggle) {
						options.onToggle.call(star, false)
					}
				});
				star.addEventListener('click', function (event) {
					if (options.onSelect.call(star, event) !== false) {
						value = star.value;
					}
				});
			});
		} else {
			el.setAttribute('aria-label', el.getAttribute('aria-label').replace('$val', value));
			el.setAttribute('role', 'img');
		}
		if (options.onLoad) {
			el.value = value;
			options.onLoad.call(el);
		}
	});
}

/* --- detail --- */
function JsRatingDetail(el, options) {
	options = Object.assign({
		value: undefined,
	}, options);

	if (!Array.isArray(options.value)) {
		options.value = el.getAttribute('data-rating-detail').split('|');
	}
	options.value = options.value.map(function (v) {
		return parseInt(v);
	});

	let total = options.value.reduce(function (t, v) {
		return t + v;
	}, 0);

	// head
	JsRating(el, {
		type: 'basic',
		span: el.querySelector('span'),
	});

	// body
	let ol = el.appendChild(JsElement('ol'));
	ol.setAttribute('aria-hidden', true);
	options.value.forEach(function (value, i) {
		let width = 0;
		if (value) {
			width = parseInt(value / total * 100);
		}

		ol.insertBefore(JsElement('li', {
			html: '<div><div class="measuring-bar"><div style="width: ' + width + '%;"></div></div></div>'
				+ '<div>' + (i + 1) + '<i class="fa-solid fa-star"></i></div>'
		}), ol.firstChild);
	});
}

/* --- felem --- */
function JsRatingHolder() {
	let el, values, dftValue;
	return {
		setElement: function (box) {
			el = box.querySelector('span');
			let value = box.getAttribute('data-value');
			if (value) {
				values = [''].concat(value.split('|'));
			}
			return this;
		},
		setDefaultValue: function (value) {
			dftValue = value;
			return this;
		},
		print: function (status, value) {
			if (!status) {
				value = dftValue;
			}
			if (values) {
				value = values[value];
			}
			el.innerHTML = '(' + value + ')';
			return this;
		}
	}
}

if (typeof JsFelem == 'object') {
	JsFelem.implement({
		rating: function (el, form) {
			let holder = JsRatingHolder();
			let name = el.getAttribute('data-name');
			JsRating(el, {
				onLoad: function () {
					holder
						.setElement(this)
						.setDefaultValue(this.value)
						.print(false);
				},
				onSelect: function () {
					holder.setDefaultValue(this.value);
					if (name) {
						form[name].value = this.value;
					}
				},
				onToggle: function (status) {
					holder.print(status, this.value);
				}
			});
		}
	});
}
