/* --- Crop Image ------------------------------------------------ */
var cropImage = function (src, callback, options, LastPosition) {
    let image = new Image();
    image.src = src;
    image.onload = function () {
        let isProportional;
        const limits = {};
        const minWH = 40;
        const wrapper = JsElement('div.crop-image', {
            html: '<div><div><div></div><div></div><div></div><div></div></div></div><button class="btn btn-primary btn-solid btn-block">OK</button>'
        });
        const lightbox = Lightbox({ hideWithOverlay: false }).setContent(wrapper);
        const container = wrapper.querySelector('div');
        const frame = container.querySelector('div');
        const handle = frame.querySelectorAll('div');
        const enter = wrapper.querySelector('button');

        // functions
        function set(values) {
            for (let i in values) {
                limits[i] = values[i];
            }
        }

        function between(v, min, max) {
            return v < min ? min : (v > max ? max : v);
        }

        function resize(styles) {
            for (let i in styles) {
                frame.style[i] = styles[i] === null ? 'initial' : styles[i] + 'px';
            }
        }

        function resizeStartFn(i) {
            return function (event) {
                event.stopPropagation();
                event.preventDefault();
                let position = {
                    top: null,
                    right: null,
                    bottom: null,
                    left: null
                };
                let a = container.getBoundingClientRect();
                let b = frame.getBoundingClientRect();

                // left - right
                switch (i) {
                    case 0:
                    case 3:
                        set({
                            iniX: event.clientX + b.width,
                            maxW: b.x - a.x + b.width,
                            signX: -1
                        });
                        position.right = a.x + a.width - b.x - b.width;
                        break;
                    case 1:
                    case 2:
                        set({
                            iniX: event.clientX - b.width,
                            maxW: a.x + a.width - b.x,
                            signX: 1
                        });
                        position.left = b.x - a.x;
                        break;
                }

                // top / bottom
                switch (i) {
                    case 0:
                    case 1:
                        set({
                            iniY: event.clientY + b.height,
                            maxH: b.y + b.height - a.y,
                            signY: -1
                        });
                        position.bottom = a.y + a.height - b.y - b.height;
                        break;
                    case 2:
                    case 3:
                        set({
                            iniY: event.clientY - b.height,
                            maxH: a.y + a.height - b.y,
                            signY: 1
                        });
                        position.top = b.y - a.y;
                        break;
                }

                resize(position);
                if (!isProportional) {
                    isProportional = event.shiftKey == 1;
                }
            };
        }

        function resizeMoveFn(event) {
            let styles = {
                width: between(limits.signX * (event.clientX - limits.iniX), minWH, limits.maxW)
            };

            if (isProportional) {
                styles.height = styles.width * options.proportion;
                if (styles.height > limits.maxH) {
                    styles.height = limits.maxH;
                    styles.width = limits.maxH / options.proportion;
                }
            } else {
                styles.height = between(limits.signY * (event.clientY - limits.iniY), minWH, limits.maxH);
            }

            resize(styles);
        }

        //
        container.insertBefore(image, container.firstChild);

        // resize frame
        for (let i = 0; i < 4; i++) {
            JsMove(handle[i], resizeStartFn(i), resizeMoveFn);
        }

        // move frame
        JsMove(frame, function (event) {
            event.stopPropagation();
            event.preventDefault();
            let a = container.getBoundingClientRect();
            let b = frame.getBoundingClientRect();

            set({
                iniX: event.clientX - b.x + a.x,
                iniY: event.clientY - b.y + a.y,
                maxX: a.width - b.width,
                maxY: a.height - b.height
            });

        },
            function (event) {
                resize({
                    left: between(event.clientX - limits.iniX, 0, limits.maxX),
                    top: between(event.clientY - limits.iniY, 0, limits.maxY)
                });
            });

        // Set options
        options = Object.assign({
            proportion: null
        }, options);

        // prepare frame
        if (typeof LastPosition != 'function') {
            LastPosition = function (position) {
                return position || {};
            };
        }
        var position = LastPosition();

        if (!Object.keys(position).length) {
            var a = container.getBoundingClientRect();
            var proportion = a.height / a.width;
            position = {
                top: 0,
                left: 0,
                width: a.width,
                height: a.height
            };

            if (options.proportion) {
                isProportional = true;

                if (proportion > options.proportion) {
                    position.height = position.width * options.proportion;
                    position.top = (a.height - position.height) / 2;
                } else {
                    position.width = position.height / options.proportion;
                    position.left = (a.width - position.width) / 2;
                }
            } else {
                options.proportion = proportion;
            }
        }

        resize(position);
        enter.addEventListener('click', function () {
            let a = container.getBoundingClientRect();
            let b = frame.getBoundingClientRect();
            let correctorX = image.naturalWidth / a.width;
            let correctorY = image.naturalHeight / a.height;
            let left = b.x - a.x;
            let top = b.y - a.y;
            let x = left * correctorX;
            let y = top * correctorY;
            let width = b.width * correctorX;
            let height = b.height * correctorY;
            //
            let canvas = JsElement('canvas');
            let ctx = canvas.getContext('2d');
            let mime = image.src.split(':')[1].split(';')[0];

            canvas.width = width;
            canvas.height = height;
            ctx.drawImage(image, x, y, width, height, 0, 0, width, height);
            callback(canvas.toDataURL(mime));
            LastPosition({
                top: top,
                left: left,
                width: b.width,
                height: b.height
            });
            lightbox.remove();
        });
    };
};