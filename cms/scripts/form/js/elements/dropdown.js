
/* --- Dropdown and Select --- */
(function () {
    function getMenu($el) {
        $menu = $el.querySelector('[role=drop-menu]')
            || $el.querySelector('.dropdown-menu');

        return $menu || getMenu($el.parentNode);
    }

    function setDropdownEvents($menu, $caret, $el) {
        function fn(event) {
            event.stopPropagation();
            JsDropdown($menu, {
                onToggle: function (status) {
                    ($el || $caret).classList.toggle('active', status);
                    $caret.setAttribute('aria-expanded', status);

                    if (status) {
                        const rect = $menu.getBoundingClientRect();

                        if (rect.right > window.innerWidth) {
                            $menu.style.right = '-1px';
                            $menu.style.left = 'auto';
                        } else if (rect.left < 0) {
                            $menu.style.right = 'auto';
                            $menu.style.left = '0px';
                        }
                    }
                },
            }).toggle();
        }

        $caret.addEventListener('click', fn);
        $caret.setAttribute('aria-expanded', false);

        const isGroup = $el.className == 'btn-group';

        if (isGroup) {
            $menu.style.right = '-1px';
        } else {
            $menu.style.left = ($el.getBoundingClientRect().left - $el.parentNode.getBoundingClientRect().left) + 'px';
        }
    }

    function setListEvents($menu, $caret, $label, $el) {
        const $hidden = $menu.querySelector('input[type=hidden]');
        const li = Array.from($menu.getElementsByTagName('LI'));

        li.forEach(($li) => {
            $li.option = /* $li.querySelector('a') || */ $li;
            $li.option.setAttribute('role', 'option');
            $li.addEventListener('click', function (event) {
                event.stopPropagation();
                JsDropdown.hide();

                li.forEach(($item) => {
                    const selected = $item == this;
                    $item.className = (selected ? 'selected' : '');
                    $li.option.setAttribute('aria-selected', selected);
                });

                $hidden.value = this.getAttribute('data-select-value');
                $label.innerHTML = this.firstChild.innerHTML;
                $label.focus();
                $el.dispatchEvent(new Event('change', { bubbles: true }));
            })
        });

        if ($caret == $el) {
            $el.setAttribute('role', 'combobox');
            $el.setAttribute('aria-haspopup', 'listbox');
        } else {
            $caret.setAttribute('role', 'combobox');
            $caret.setAttribute('aria-haspopup', 'listbox');
        }
    }

    function select($el) {
        const $menu = getMenu($el);
        const $caret = $el.querySelector('.btn-caret') || $el;
        const $label = $el.querySelector('[data-select-label]') || $el;

        setDropdownEvents($menu, $caret, $el);
        setListEvents($menu, $caret, $label, $el);

        if ($el.getAttribute('on-change') == 'submit') {
            $el.addEventListener('change', () => JsFelem.submit($el));
        }
    }

    function dropdown($el) {
        setDropdownEvents(getMenu($el), $el, $el);
    }

    // felem implement
    JsFelem.implement({ select, dropdown });
})();