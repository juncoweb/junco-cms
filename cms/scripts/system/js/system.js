/**
 * Merge Options
 */
function JsMergeOptions(base, options, force) {
    options = Object.assign(base, options, force);

    var events = {};
    for (var i in options) {
        if (
            i.substring(0, 2) == 'on'
            && typeof options[i] == 'function'
        ) {
            events[i.substring(2).toLowerCase()] = options[i];
            delete options[i];
        }
    }

    options.fire = function (name, scope, ...args) {
        if (events[name]) {
            return events[name].apply(scope, ...args);
        }
    }

    return options;
}

/**
 * Normalize Elements (to array)
 */
function Iterable(elements) {
    if (!elements) {
        return [];
    } else if (elements.nodeType == 1) {
        elements = [elements];
    } else {
        switch (Object.prototype.toString.call(elements).slice(8, -1)) {
            case 'Object':
            case 'HTMLCollection':
            case 'NodeList':
                let i = elements.length || 0;
                let a = new Array(i);
                while (i--) {
                    a[i] = elements[i];
                }
                elements = a;
            // break;
            case 'Array':
                break;
            default:
                return [];
        }
    }
    return elements;
}