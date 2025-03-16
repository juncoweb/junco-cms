
/**
 * Slideshow
 *
 * @arguments
 * element - (string or object) element or ID to insert the gallery
 * options - (object)
 *
 * @options:
 * curFrame - (number) the current frame
 *
 * controls - (strin or array) the display options: "arrows,nav"
 * navReturn - (boolean) returns the last frame at the beginning and vice versa
 *
 * @events
 * onLoad - (function)
 * onChange - (function)
 */

var Slideshow = function (element, options) {

	// vars
	var _element = typeof element == 'string' ? document.querySelector(element) : element;

	if (!_element) {
		return;
	}

	// 
	var _film = _element.querySelector('ul'),
		_frames = _film.querySelectorAll('li'),
		_numFrames = _frames.length,
		_lastFrame = _numFrames - 1,
		_isLastFrame,
		_prevFrame,
		_curFrame,
		_curLeft;

	//
	var _handle;
	var _onChange = [];

	// options
	var _options = {
		// main
		curFrame: 0,

		// behavior
		navReturn: 0,		// return the car to start when it ends
		setInterval: 5000,	// 5000 miliseconds

		// controls
		controls: 'arrows,nav', // arrows,nav

		// events
		onLoad: null,		// on change plugin
		onChange: null,		// on change plugin
	};

	// methods
	var that = {
		goTo: function (enter, force) {
			if (enter > _lastFrame) {
				enter = !(force || _options.navReturn) ? _lastFrame : 0;
			} else if (enter < 0) {
				enter = !(force || _options.navReturn) ? 0 : _lastFrame;
			}

			// vars
			_prevFrame = _curFrame;
			_curFrame = enter;
			_curLeft = -_curFrame * 100;
			_isLastFrame = (_curFrame == _lastFrame);

			// onChange
			_onChange.forEach(function (fn) { fn(that); });

			// take
			_film.style.marginLeft = _curLeft + '%';

			//
			for (var i = 0; i < _numFrames; i++) {
				_frames[i].className = (i == _curFrame ? 'enabled' : 'disabled');
			}
		},

		next: function (force) {
			this.goTo(_curFrame + 1, force);
		},

		prev: function (force) {
			this.goTo(_curFrame - 1, force);
		},
	};


	// set options
	for (var i in options) {
		if (typeof _options[i] != 'undefined') {
			_options[i] = options[i];
		}
	}

	// controls
	switch (typeof _options.controls) {
		case 'string': _options.controls = _options.controls.split(',');
		case 'object':
			// create
			_options.controls.forEach(function (opt) {
				switch (opt) {
					case 'arrows':
						var _prev = _element.appendChild(JsElement('div.ss-prev', {
							html: '<span></span>', // &#10092; &#10093;
							events: {
								click: function () { that.prev(); }
							}
						}));
						var _next = _element.appendChild(JsElement('div.ss-next', {
							html: '<span></span>',
							events: {
								click: function () { that.next(); }
							}
						}));

						if (!_options.navReturn) {
							_onChange.push(function () {
								_prev.style.visibility = !_curFrame ? 'hidden' : 'visible';
								_next.style.visibility = _isLastFrame ? 'hidden' : 'visible';
							});
						}
						break;

					case 'nav':
						for (var i = 0, html = ''; i < _numFrames; i++) {
							html += '<li><div></div></li>';
						}
						var bullets = _element.appendChild(JsElement('DIV.ss-nav', {
							html: '<ul>' + html + '</ul>',
						})).firstChild.childNodes;

						for (i = 0; i < _numFrames; i++) (function (el, i) {
							el.addEventListener('click', function () { that.goTo(i); });
						})(bullets[i], i);

						_onChange.push(function () {
							for (var j = 0; j < _numFrames; j++) {
								bullets[j].className = (j == _curFrame ? 'selected' : '');
							}
						});
						break;
				}
			});
	}

	// prepare
	_film.style.width = (_numFrames * 100) + '%';
	_film.style.marginLeft = '0%';


	// events
	if (_options.setInterval) {
		_onChange.push(function () {
			if (_handle) {
				clearTimeout(_handle);
			}
			_handle = setTimeout(function () { that.next(1); }, _options.setInterval);
		});
	}

	if (typeof _options.onChange == 'function') {
		_onChange.push(function () { _options.onChange(that); });
	}

	var isFade = _element.classList.contains('fade');
	if (isFade) {
		if (_film.querySelector('li > img')) {
			_element.classList.remove('fade');
		} else {
			var _img = _film.querySelectorAll('li');

			_onChange.push(function () {
				if (_prevFrame !== undefined) {
					_element.style.backgroundImage = _img[_prevFrame].style.backgroundImage;
				}
			});
		}
	}

	var ratio = _element.getAttribute('data-ratio');
	if (ratio) {
		ratio = ratio.split('x');

		var isResponsive = _element.classList.contains('responsive');
		var iProp = ratio[1] / ratio[0]; // height / width
		var fn = function () {
			let value;
			if (isResponsive) {
				let wProp = window.innerHeight / window.innerWidth;
				value = (iProp > wProp ? iProp : wProp);
			} else {
				value = iProp;
			}

			_film.style.height = Math.ceil(value * _film.getBoundingClientRect().width / _numFrames) + 'px';
		};

		fn();
		window.addEventListener('resize', fn);
	}

	// touch event
	if (!isFade) {
		var initialX, overflow = 16;
		var corrector = 100 * _numFrames / parseInt(_film.getBoundingClientRect().width); // transform pixels to percent
		var maxMove = -100 * (_numFrames - 1) - overflow;

		function starFn(event) {
			event.stopPropagation();
			//event.preventDefault();

			_film.style.transition = 'initial';
			initialX = event.clientX;
		}

		function moveFn(event) {
			var v = -(_curFrame * 100 + ((initialX - event.clientX) * corrector));
			if (v > overflow || v < maxMove) {
				return;
			}

			_film.style.marginLeft = v + '%';
		}

		function endFn(event) {
			var v = (initialX - event.clientX) * corrector;
			that.goTo(_curFrame + (Math.abs(v) < 10 ? 0 : (v > 0 ? 1 : -1)));
			_film.style.transition = '';
		}

		JsMove(_film, starFn, moveFn, endFn);
	}

	// take
	that.goTo(parseInt(_options.curFrame));

	if (typeof _options.onLoad == 'function') {
		_options.onLoad(this);
	}

	return that;
};



Slideshow.pool = function (el, fn, cSelector, iSelector) {
	let json = [];

	if (typeof el == 'object') {
		let img = el.querySelectorAll(iSelector || 'img');
		let length = img.length;

		for (let i = 0; i < length; i++) {
			json[i] = { src: img[i].src, title: img[i].title };
		}

		Array.from(el.querySelectorAll(cSelector || 'img'))
			.forEach(function (ctrl, i) {
				ctrl.addEventListener('click', function () {
					fn(i);
				});
			});
	}

	return json;
};

Slideshow.create = function (box, json) {
	var html = '';
	var length = json.length;

	for (var i = 0; i < length; i++) {
		//(json[i].title
		html += '<li><img'
			+ (typeof json[i].srcset == 'string' ? ' srcset="' + json[i].srcset + '"' : '')
			+ (typeof json[i].sizes == 'string' ? ' sizes="' + json[i].sizes + '"' : '')
			+ ' src="' + json[i].src + '"/></li>';
	}

	box.innerHTML = '<div class="slideshow slide"><ul>' + html + '</ul></div>';
	return box.firstChild;
};
