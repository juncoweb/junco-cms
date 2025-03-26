
/* --- Suite ----------------------------------------------------------- */
JsFelem.implement({
    suite: function (el) {
        var name = el.getAttribute('data-name');
        var box = el.querySelectorAll('li');
        var All = box[1].querySelectorAll('label');
        var selected = el.getAttribute('data-selected');
        var checkAll = el.querySelector('input[type=checkbox]');

        var Has = {};
        var _count = 0;
        var _total = All.length;

        // functions
        function reset(_selected) {
            // clear
            box[0].querySelectorAll('input').forEach(function (el) {
                Has[el.value](0);
            });

            if (typeof _selected != 'string') {
                _selected = selected;
            }

            // put selected
            if (_selected) {
                _selected.split(',').forEach(function (k) {
                    if (typeof Has[k] != 'undefined') {
                        Has[k](1);
                    }
                });
            }
        }

        function dragAndDrop(tag) {
            var corrector, current;

            JsMove(tag, function () {
                var target = tag.getBoundingClientRect();
                var rect = box[0].getBoundingClientRect();

                corrector = {
                    x: target.width / 2 + rect.left + (window.pageXOffset || document.documentElement.scrollLeft),
                    y: target.height / 2 + rect.top + (window.pageYOffset || document.documentElement.scrollTop)
                };
            },
                function (event) {
                    tag.style.position = 'absolute';
                    tag.style.left = (event.pageX - corrector.x) + 'px';
                    tag.style.top = (event.pageY - corrector.y) + 'px';

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
                        box[0].querySelectorAll('label').forEach(function (x) {
                            var target = x.getBoundingClientRect();
                            if (x != tag
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
                        var target = tag.getBoundingClientRect();
                        var rect = current.getBoundingClientRect();

                        if (rect.left + rect.width / 2 < target.left + target.width / 2) {
                            if (current.nextSibling) {
                                box[0].insertBefore(tag, current.nextSibling);
                            } else {
                                box[0].appendChild(tag);
                            }
                        } else {
                            box[0].insertBefore(tag, current);
                        }

                        current = null;
                    }
                    tag.style.position = '';
                    tag.style.left = '';
                    tag.style.top = '';
                });
        }

        //
        All.forEach(function (el) {
            var tag;
            var isSelected = el.classList.contains('selected');
            var value = el.getAttribute('data-value');
            var fn = function (show) {
                if (isSelected === show) {
                    return;
                }

                if (typeof tag == 'undefined') {
                    tag = JsElement('label.input-tag selected', {
                        html: '<input type="hidden" name="' + name + '[]" value="' + value + '"/>' + el.innerHTML
                    });

                    dragAndDrop(tag);
                }

                if (show) {
                    box[0].appendChild(tag);
                    el.classList.add('selected');
                    _count++;
                } else {
                    box[0].removeChild(tag);
                    el.classList.remove('selected');
                    _count--;
                }

                checkAll.checked = (_count == _total);
                isSelected = show;
            };

            //
            el.value = value;
            el.addEventListener('click', function () {
                fn(el.classList.contains('selected') ? 0 : 1);
            });

            Has[value] = fn;
        });

        // buttons
        var toggle = function (force) {
            var v = ['', 'none', ''];

            box[0].style.display = v[force];
            box[1].style.display = v[force + 1];
            btn.forEach(function (el, j) {
                el.style.display = v[force + (j == 3 ? 0 : 1)];
            });
        };

        checkAll.addEventListener('change', function () {
            var force = checkAll.checked == true ? 1 : 0;

            All.forEach(function (el) {
                Has[el.value](force);
            });
        });

        var btn = el.querySelectorAll('div');
        btn[0].addEventListener('click', function () { toggle(0) });
        btn[1].addEventListener('click', reset);
        btn[3].addEventListener('click', function () { toggle(1) });

        toggle(1);
        reset(selected); // Show selected

        // prepare form element
        el.value = selected;
        el.type = 'suite';
        el.reset = reset;
    }
});
