/* --- Upload Handle -------------------------------------------------- */
const UploadHandle = function (el, form, data) {
	var _data;
	var WH = ['width', 'height'];
	var options = {
		thumb_wh: 78,
		max_wh: 1200,
		max_size: 0,
		proportion: 0,
		accept: null,
		images: null,
		caption: null,
		reorder: false,
	};

	// functions
	function resize(img, max_wh) {
		let x = 0;
		let y = 0;
		let width = img.width;
		let height = img.height;

		if (options.proportion) {
			let proportion = height / width;

			if (proportion > options.proportion) {
				height = width * options.proportion;
				y = (img.height - height) / 2;
			} else {
				width = height / options.proportion;
				x = (img.width - width) / 2;
			}
		}

		let maj = WH[width > height ? 0 : 1];

		if (x || y || img[maj] > max_wh) {
			let min = WH[maj == WH[0] ? 1 : 0];
			let minWH = width > height ? height * max_wh / width : width * max_wh / height;
			let canvas = JsElement('canvas');
			let ctx = canvas.getContext('2d');

			canvas[maj] = max_wh;
			canvas[min] = Math.round(minWH);
			ctx.drawImage(img, x, y, width, height, 0, 0, canvas.width, canvas.height);
			return canvas.toDataURL(getMimeType(img.src));
		}

		return img.src;
	}

	// helpers
	function getMimeType(src) {
		return src.split(':')[1].split(';')[0];
	}

	function dataURItoBlob(data) {
		data = data.split(',');
		let mime = getMimeType(data[0]);
		let bytes = atob(data[1]);
		let length = bytes.length;
		let buffer = new ArrayBuffer(length);
		let uint8 = new Uint8Array(buffer);

		for (let i = 0; i < length; i++) {
			uint8[i] = bytes.charCodeAt(i);
		}

		return new Blob([new DataView(buffer)], {
			type: mime
		});
	}

	function getAccept(accept) {
		if (!accept) {
			return '*/*';
		}

		if (['audio', 'image', 'video'].includes(accept)) {
			return accept + '/*';
		}

		return accept;
	}

	function createElement(el) {
		let container = JsElement('div.input-file' + (isMultiple ? ' multiple' : ''));
		let AddButton = container.appendChild(JsElement('div', {
			html: '<div class="uh-icon"><i class="fa-solid fa-cloud-arrow-up"></i></div><div>' + options.caption + '</div>',
			'class': 'input-field uh-btn',
			tabindex: 0,
			role: 'button',
			events: {
				click: function (event) {
					event.stopPropagation();
					var f = document.body.appendChild(JsElement('input', {
						type: 'file',
						accept: getAccept(options.accept),
						multiple: isMultiple,
						styles: {
							display: 'none',
						},
						events: {
							change: function () {
								addFiles(f);
							},
						},
					}));
					f.click();
				},
				keydown: function (event) {
					if (event.key == 'Enter') {
						event.preventDefault();
						event.target.click();
					}
				},
				dragover: function (event) {
					event.preventDefault();
				},
				drop: function (event) {
					event.preventDefault();
					if (event.dataTransfer.files.length) {
						addFiles(event.dataTransfer);
					}
				},
			},

			toggle: function (status) {
				this.style.display = status ? '' : 'none';
				if (status) {
					this.focus();
				}
			},
		}));

		function addFiles(el) {
			if (!isMultiple) {
				AddButton.toggle(false);
			}

			var length = el.files.length;

			for (var i = 0; i < length; i++) {
				(function (file) {
					var frame = newFrame(file.name);
					var reader = new FileReader();
					reader.readAsDataURL(file);
					reader.onprogress = function (response) {
						frame.proggressBar(Math.round(response.loaded / response.total) * 100);
					};

					reader.onload = function () {
						frame.proggressBar(-1);
						if (file.type.match('image.*')) {
							var img = new Image();
							img.src = this.result;
							img.onload = function () {
								let src = resize(this, options.max_wh);

								// thumb
								frame.removeTitle();
								frame.addThumb(src);

								// append
								frame.appendData(dataURItoBlob(src), file.name);
								frame.addCropImage(this.src, dataID);
							};
						} else {
							frame.addFileIcon();
							frame.appendData(file, file.name);
						}
					};
					//reader.onerror = errorHandler;
					//reader.onabort = function(e) { alert('File read cancelled'); };
				})(el.files[i]);
			}
		}

		var hidden;

		function reorder() {
			if (!hidden) {
				hidden = form.appendChild(JsElement('input', {
					'type': 'hidden',
					'name': '__' + _name.substr(0, _name.length - 2)
				}));
			}

			let names = [];
			Array.from(container.querySelectorAll('div > div[data-name]'))
				.forEach(function (frame) {
					names.push(frame.getAttribute('data-name'));
				});

			hidden.value = names.join('|');
		}

		function newFrame(title) {
			let attr = {
				html: '<div>' + (title ? '<div class="uh-title">' + title + '</div>' : '') + '</div>',
				tabindex: 0,
				title: title,
				'aria-label': title,
				events: {
					keydown: function (event) {
						event.stopPropagation();
						if (event.key == 'Delete') {
							remove();
						} else if (event.key == 'Enter') {
							let el = frame.querySelector('.uh-crop');
							if (el) {
								el.click();
							}
						}
					}
				}
			};

			if (options.reorder) {
				Object.assign(attr, {
					draggable: true,
					events: {
						dragstart: function () {
							container.curDrag = this;
						},
						dragover: function (event) {
							event.preventDefault();
							event.dataTransfer.dropEffect = 'move';
							return false;
						},
						drop: function (event) {
							event.preventDefault();
							var c = container.curDrag;
							if (c) {
								switch (c.compareDocumentPosition(this)) {
									case 4:
										if (this.nextSibling) {
											container.insertBefore(c, this.nextSibling);
										} else {
											container.appendChild(c);
										}
										break;

									case 2:
										container.insertBefore(c, this);
										break;
								}
							}
							reorder();
							return (container.curDrag = false);
						}
					}
				});
			}

			let frame = container.appendChild(JsElement('div.input-field', attr));
			let box = frame.querySelector('div');

			function remove() {
				if (typeof dataID != 'undefined') {
					_data[--dataID] = false;
				}

				container.removeChild(frame);
				reorder();
				AddButton.toggle(true);
			}

			function addThumb(img, name) {
				if (typeof img == 'string') {
					var _img = new Image();
					_img.src = img;
					_img.onload = function () {
						//this[WH[this.width > this.height ? 0 : 1]] = options.thumb_wh;
						addThumb(this);
					};
					if (name) {
						frame.appendChild(JsElement('input', {
							'type': 'hidden',
							'name': name,
							'value': 1
						}));
					}
				} else {
					let el = box.querySelector('img');
					if (el) {
						el.parentNode.removeChild(el);
					}
					box.parentNode.classList.add('uh-img');
					box.appendChild(img);
				}
				return this;
			}

			function addCross() {
				box.appendChild(JsElement('div.uh-cross', {
					html: '<i class="fa-solid fa-xmark"></i>',
					events: {
						click: function () {
							remove();
						}
					}
				}));
				return this;
			}

			return {
				proggressBar: (function () {
					var bar;

					return function (value) {
						if (value == -1) {
							return box.removeChild(bar);
						} else if (!bar) {
							bar = box.appendChild(JsElement('div.uh-proggress'));
						}
						bar.style.width = value + '%';
					};
				})(),

				removeTitle() {
					box.removeChild(box.querySelector('.uh-title'));
				},

				addThumb: addThumb,
				addCross: addCross,
				addCropImage: function (src, dataID) {
					var LastPosition = (function () {
						var position = {};
						return function (value) {
							if (value) {
								position = value;
							}
							return position;
						};
					})();

					box.appendChild(JsElement('div.uh-crop', {
						html: '<i class="fa-solid fa-crop-simple"></i>',
						events: {
							click: function () {
								cropImage(src, function (src) {
									var img = new Image();
									img.src = src;
									img.onload = function () {
										let src = resize(this, options.max_wh);
										_data[dataID - 1][1] = dataURItoBlob(src);
										addThumb(src);
										frame.focus();
									};
								}, options, LastPosition);
							}
						}
					}));
					return this;
				},
				appendData: function (src, filename) {
					if (options.max_size && options.max_size < src.size) {
						box.innerHTML = '<div class="uh-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>';
					} else {
						dataID = _data.push([_name, src, filename]);
					}

					if (isMultiple) {
						this.setName(filename);
					}

					addCross();
				},

				remove: remove,
				setName: function (v) {
					box.setAttribute('data-name', v);
					reorder();
				},

				addFileIcon: function () {
					box.innerHTML = '<div class="uh-icon"><i class="fa-regular fa-file"></i></div>' + box.innerHTML;
				}
			};
		}


		el.parentNode.insertBefore(container, el);
		el.parentNode.removeChild(el);

		return {
			addFrame: function (title) {
				if (!isMultiple) {
					AddButton.toggle(false);
				}
				return newFrame(title);
			},
		};
	};

	// get form
	if (!form || form.tagName != 'FORM') {
		for (form = el; form.tagName != 'FORM'; form = form.parentNode) {
			if (form.tagName == 'BODY') {
				return;
			}
		}
	}

	// set options
	try {
		Object.assign(options, JSON.parse(el.getAttribute('data-options')));
	} catch (e) { }

	_data = data || [];
	var _name = el.name;
	var isMultiple = 'multiple' in JsElement('input') && el.multiple;

	// proportional
	if (typeof options.max_wh != 'number') {
		if (options.max_wh.indexOf('x') > -1) {
			let wh = options.max_wh.split('x', 2).map(v => parseInt(v));
			if (wh[0] > 0) {
				options.proportion = wh[1] / wh[0];
			}
			options.max_wh = wh[0] > wh[1] ? wh[0] : wh[1];
		} else {
			options.max_wh = parseInt(options.max_wh);
		}
	}

	// create
	UH = createElement(el);

	// default images
	if (options.images && options.images[1]) {
		if (isMultiple) {
			options.images[1].forEach(function (v) {
				UH.addFrame()
					.addThumb(options.images[0] + v)
					.addCross()
					.setName(v);
			});
		} else {
			UH.addFrame()
				.addThumb(options.images[0] + options.images[1], _name)
				.addCross();
		}
	}

	// reset form
	form.addEventListener('reset', function () {
		var frames = UH.querySelectorAll('.uh-cross');
		var i = frames.length;
		while (i--) {
			frames[i].click();
		}
	});
};

// implement
JsFelem.implement({
	file: function (el, form, data) {
		UploadHandle(el, form, data);
	}
});