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

    // tablist
    if (typeof tablist === 'string') {
        tablist = document.querySelector(tablist);
    }

    if (!(tablist instanceof Element)) {
        return null;
    }

    if (typeof options.onSelect !== 'function') {
        options.onSelect = null;
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
    const tabs = getChildNodes(tablist, 'LI');
    const panels = getChildNodes(tabpanel, 'DIV');
    const total = tabs.length;
    const lastTab = total - 1;

    // props & events
    tabs.forEach(function ($tab, index) {
        $tab.setAttribute('aria-setsize', total);
        $tab.setAttribute('aria-posinset', index + 1);
        $tab.setAttribute('tabindex', 0);
        $tab.addEventListener('click', (event) => {
            event.preventDefault();
            that.select(index);
        });
        $tab.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                document.activeElement.click();
            } else if (event.key === 'ArrowLeft') {
                that.prev().focus();
            } else if (event.key === 'ArrowRight') {
                that.next().focus();
            }
        });
    });


    const that = {
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

                for (let status, i = 0; i < total; i++) {
                    status = (i == index);

                    tabs[i].setAttribute('aria-selected', status);
                    tabs[i].setAttribute('tabindex', status ? 0 : -1);
                    tabs[i].classList.toggle('selected', status);
                    panels[i].classList.toggle('selected', status);
                    options.onSelect?.call(that, i, status);
                }
                if (handle) {
                    clearTimeout(handle);
                }
                handle = setTimeout(() => panels[index].classList.add('active'), 10);
            }

            return this;
        },

        prev: function () {
            return this.select(selected - 1);
        },

        next: function () {
            return this.select(selected + 1);
        },

        focus: function () {
            tabs[selected].focus();
            return this;
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
