/**
 * JsElement is a function that creates a new HTML element with the given tag and options.
 * It also sets attributes, styles, events, and other properties based on the provided options.
 *
 * @param {HTMLElement|string} el - The HTML element or tag name to create.
 * @param {object} options - An object containing options for the element.
 * @returns {HTMLElement} - The created HTML element.
 */
function JsElement(el, options) {
    function setAttribute(attrName, attrValue) {
        switch (attrName) {
            case 'styles':
                for (let j in attrValue) {
                    el.style[j] = attrValue[j];
                }
                break;

            case 'events':
                for (let j in attrValue) {
                    j.split(' ').forEach(function (eventName) {
                        el.addEventListener(eventName, attrValue[j]);
                    });
                }
                break;

            case 'html':
                el.innerHTML = attrValue;
                break;

            case 'text':
                el.textContent = attrValue;
                break;

            case 'class':
            case 'className':
                el.className = attrValue;
                break;

            case 'multiple':
                el.multiple = attrValue;
                break;

            default:
                if (typeof attrValue == 'function') {
                    el[attrName] = attrValue;
                } else {
                    el.setAttribute(attrName, attrValue);
                }
                break;
        }
    }

    function cleanTagName(tag) {
        if (tag.indexOf('.') > -1) {
            [tag, options.className] = tag.split('.');

            if (options.className.indexOf('#') > -1) {
                [options.className, options.id] = tag.split('#');
            }
        }

        if (tag.indexOf('#') > -1) {
            [tag, options.id] = tag.split('#');
        }

        return tag.toUpperCase();
    }

    if (typeof options != 'object') {
        options = {};
    }

    if (typeof el == 'string') {
        el = document.createElement(cleanTagName(el));
    }

    for (let i in options) {
        setAttribute(i, options[i]);
    }

    return el;
}