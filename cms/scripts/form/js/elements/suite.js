
/* --- Suite --- */
JsFelem.implement({
    suite: function (el) {
        const name = el.getAttribute('data-name');
        const $box0 = el.querySelector('.box-0');
        const $box1 = el.querySelector('.box-1');
        const $actions = el.querySelector('.actions');
        const labels = $box0.querySelectorAll('label');
        const strDft = el.getAttribute('data-selected');
        const dft = strDft ? strDft.split(',') : [];
        const $chkall = el.querySelector('input.input-checkbox');
        const total = labels.length;

        // functions
        function Checks(box) {
            return box.querySelectorAll('input[type="checkbox"]:checked').length;
        }

        function getValue(value) {
            if (typeof value == 'undefined') {
                return dft;
            }
            if (!value) {
                return [];
            }
            if (typeof value == 'string') {
                return value.split(',');
            }

            return Array.isArray(value)
                ? value
                : [];
        }

        function reset(value) {
            value = getValue(value);

            labels.forEach(($label) => $label.toggle(
                value.includes($label.value())
            ));
        }

        function DragAndDrop($tag) {
            var corrector, current;

            JsMove($tag, function () {
                var target = $tag.getBoundingClientRect();
                var rect = $box1.getBoundingClientRect();

                corrector = {
                    x: target.width / 2 + rect.left + (window.pageXOffset || document.documentElement.scrollLeft),
                    y: target.height / 2 + rect.top + (window.pageYOffset || document.documentElement.scrollTop)
                };
            },
                function (event) {
                    $tag.style.position = 'absolute';
                    $tag.style.left = (event.pageX - corrector.x) + 'px';
                    $tag.style.top = (event.pageY - corrector.y) + 'px';

                    if (current) {
                        var target = current.getBoundingClientRect();
                        if (!(target.top < event.clientY
                            && target.bottom > event.clientY
                            && target.left < event.clientX
                            && target.right > event.clientX
                        )) {
                            current = null;
                        }
                    }

                    if (!current) {
                        $box1.querySelectorAll('label').forEach(function (x) {
                            var target = x.getBoundingClientRect();
                            if (x != $tag
                                && target.top < event.clientY
                                && target.bottom > event.clientY
                                && target.left < event.clientX
                                && target.right > event.clientX
                            ) {
                                current = x;
                            }
                        });
                    }
                },
                function () {
                    if (current) {
                        var target = $tag.getBoundingClientRect();
                        var rect = current.getBoundingClientRect();

                        if (rect.left + rect.width / 2 < target.left + target.width / 2) {
                            if (current.nextSibling) {
                                $box1.insertBefore($tag, current.nextSibling);
                            } else {
                                $box1.appendChild($tag);
                            }
                        } else {
                            $box1.insertBefore($tag, current);
                        }

                        current = null;
                    }
                    $tag.style.position = '';
                    $tag.style.left = '';
                    $tag.style.top = '';
                });
        }

        function KeyboardControl($tag) {
            $tag.addEventListener('keydown', function (event) {
                if (event.key === 'ArrowLeft') {
                    if (this.previousSibling) {
                        $box1.insertBefore(this, this.previousSibling);
                    }
                } else if (event.key === 'ArrowRight') {
                    if (this.nextSibling) {
                        $box1.insertBefore(this.nextSibling, this);
                    }
                }
                this.focus();
            });
        }

        function ReadOnly($tag) {
            $tag.querySelector('input')
                .addEventListener('click', (event) => event.preventDefault());
        }

        function dpl(el, status) {
            el.style.display = ['', 'none', ''][status];
        };

        //
        JsTabs(el.firstChild).select();

        labels.forEach(function ($label) {
            const $chk = $label.querySelector('input');
            const $tag = JsElement('label.btn btn-small', {
                html: '<input type="checkbox" class="input-hidden" name="' + name + '[]" value="' + $chk.value + '" checked />' + $label.querySelector('span').innerHTML
            });

            DragAndDrop($tag);
            KeyboardControl($tag);
            ReadOnly($tag);

            $label.value = function () {
                return $chk.value
            };
            $label.toggle = function (checked) {
                if (checked) {
                    $box1.appendChild($tag);
                } else if ($tag.parentNode) {
                    $box1.removeChild($tag);
                }

                $chk.checked = checked;
                $chkall.checked = (Checks($box0) == total);
            };

            $chk.addEventListener('change', () => $label.toggle($chk.checked));
        });

        $chkall.addEventListener('change', () => {
            const force = $chkall.checked;
            labels.forEach(($label) => $label.toggle(force));
        });

        $actions
            .querySelector('button')
            .addEventListener('click', () => reset());

        reset(dft);

        // prepare form element
        el.value = dft.join(',');
        el.type = 'suite';
        el.reset = reset;
    }
});
