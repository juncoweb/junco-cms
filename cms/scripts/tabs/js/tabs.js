/**
 * Tabs
 *
 * @arguments
 * tablist - (string or object) the box of tablist
 * options - (object, optional)
 *
 * @options:
 * 
 * @events:
 * onSelect
 *
 */
function JsTabs(tablist, options) {
    options = Object.assign({}, options);

    if (typeof options.onSelect !== 'function') {
        options.onSelect = null;
    }

    // tablist
    if (typeof tablist === 'string') {
        tablist = document.querySelector(tablist);
    }

    if (!(tablist instanceof Element)) {
        return null;
    }

    // tabpanel
    for (var tabpanel = tablist.nextSibling; tabpanel.nodeType != 1; tabpanel = tabpanel.nextSibling);

    function getChildNodes(el, tagName) {
        let nodes = [];
        for (let i = 0, L = el.childNodes.length; i < L; i++) {
            if (el.childNodes[i].tagName == tagName) {
                nodes.push(el.childNodes[i]);
            }
        }

        return nodes;
    }

    let handle, selected;
    let tabs = getChildNodes(tablist, 'LI');
    let panels = getChildNodes(tabpanel, 'DIV');
    let total = tabs.length;
    let lastTab = total - 1;

    // props & events
    tabs.forEach(function (el, index) {
        el.setAttribute('aria-setsize', total);
        el.setAttribute('aria-posinset', index + 1);
        el.setAttribute('tabindex', 0);
        el.addEventListener('click', function (event) {
            event.preventDefault();
            that.select(index);
        });
        el.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                document.activeElement.click();
            } else if (event.key === 'ArrowLeft') {
                that.prev();
                tabs[selected].focus();
            } else if (event.key === 'ArrowRight') {
                that.next();
                tabs[selected].focus();
            }
        });
    });


    let that = {
        /**
         * select
         *
         * @params:
         * index - (number). index Tab to be selectedTab
         */
        select: function (index) {
            if (typeof index != 'number' || index > lastTab) {
                index = 0;
            } else if (index < 0) {
                index = lastTab;
            }

            // make the tablist changes
            if (index != selected) {
                selected = index;
                let status;

                for (var i = 0; i < total; i++) {
                    status = (i == index);

                    tabs[i].setAttribute('aria-selected', status);
                    tabs[i].setAttribute('tabindex', status ? 0 : -1);
                    tabs[i].className =
                        panels[i].className = (status ? 'selected' : '');

                    if (options.onSelect) {
                        options.onSelect.call(that, i, status);
                    }
                }
                if (handle) {
                    clearTimeout(handle);
                }
                handle = setTimeout(function () { panels[index].classList.add('active') }, 10);
            }
            return this;
        },

        prev: function () {
            this.select(selected - 1);
        },

        next: function () {
            this.select(selected + 1);
        },

        selectedTabNumber: function () {
            return selected;
        },

        getContainer: function (number = -1) {
            if (number < 0) {
                number = selected;
            }
            return panels[number];
        },

        isEmpty: function (number, spinner = true) {
            if (selected == number && !panels[number].innerHTML) {
                if (spinner) {
                    panels[number].innerHTML = '<div class="box-loading"><i class="fa-solid fa-circle-notch fa-spin"></i></div>';
                }
                return true;
            }
            return false;
        },
    };

    return that;
}
