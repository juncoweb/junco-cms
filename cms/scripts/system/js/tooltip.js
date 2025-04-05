
/* --- Tooltip --- */
const Tooltip = function (box) {
    box = (box || document);

    const tt = (el) => {
        let tooltip, parent, status;
        let content = el.getAttribute('data-value');

        // I look for the tooltip element
        if (content === null) {
            content = el.title;
            el.title = '';
        }

        if (!content) {
            return;
        }

        if (content.charAt(0) == '#') { // I'm looking for an element with id
            tooltip = box.querySelector(content);
        }

        if (tooltip) {
            for (
                parent = tooltip.parentNode;
                parent && window.getComputedStyle(parent).position != 'relative';
                parent = parent.parentNode == document.body ? false : parent.parentNode
            );
        } else {
            tooltip = document.body.appendChild(JsElement('div.tooltip', { 'html': content }));
        }

        // set options
        let position = 'top';
        let delay = 0;

        el.getAttribute('data-tooltip').split(' ').forEach((cmd) => {
            if (cmd == 'blocked') {
                delay = 100;
            } else if (['bottom', 'right', 'left'].indexOf(cmd) > -1) {
                position = cmd;
                tooltip.classList.add(cmd);
            }
        });

        // toggle method
        tooltip.toggle = function (force) {
            switch (force) {
                case 2:
                    if (!status) {
                        status = 1;
                    }
                case 1:
                    if (status) {
                        return;
                    }
                    status = 1;
                    break;
                case 0:
                    status = 0;
                    if (delay) {
                        return setTimeout(() => { tooltip.toggle(-1) }, delay);
                    }
                    break;
                case -1:
                    if (status) {
                        return;
                    }
                case -2:
                    force = 0;
                    break;
            }

            if (force) { // I calculate the position
                let top, left;
                let rectA = el.getBoundingClientRect();
                let rectB = tooltip.getBoundingClientRect();
                let rectC = parent ? parent.getBoundingClientRect() : {
                    top: -document.documentElement.scrollTop,
                    left: -document.documentElement.scrollLeft
                };

                switch (position) {
                    case 'top':
                        top = (rectA.top - rectB.height - 5);
                        break;
                    case 'bottom':
                        top = (rectA.top + rectA.height + 5);
                        break;
                    case 'right':
                        left = (rectA.left + rectA.width + 5);
                        break;
                    case 'left':
                        left = (rectA.left - rectB.width - 5);
                        break;
                }

                if (!top) {
                    top = rectA.top + rectA.height / 2 - rectB.height / 2;
                } else if (!left) {
                    left = rectA.left + rectA.width / 2 - rectB.width / 2;
                }

                tooltip.style.top = top - rectC.top + 'px';
                tooltip.style.left = left - rectC.left + 'px';
                TooltipActive.add(this);
            }

            this.classList.toggle('show', force);
        };

        // set events
        const events = (el, statuses) => {
            statuses.forEach((status, i) => {
                el.addEventListener(['mouseout', 'mouseover', 'focus', 'blur'][i], () => {
                    tooltip.toggle(status);
                });
            });
        }

        events(el, [0, 1, 1, 0]);

        if (delay) {
            events(tooltip, [0, 2]);
        }
    };

    Array.from(box.querySelectorAll('[data-tooltip]')).forEach(tt);
};

const TooltipActive = (function () {
    let current;
    return {
        add: function (el) {
            this.hide();
            current = el;
        },
        hide: function (el) {
            if (current) {
                current.toggle(-2);
            }
        },
    };
})();