
/**
 * Collection
 *
 * @ creates list of data asynchronously
 * @ There are 3 types
 *  - select
 *  - select multiple
 *  - mapper			// does not create any input
 *
 * @ Select event has
 *   onSelect(value, caption) {
 *      this.input;				// the input element + set(), reset()
 *      this.hidden;			// the hidden element + set(), reset()
 *      this.picker;			// the picker element + show(), hide(), select()
 *      return (boolean)		// false to stop default
 *   }
 */
function JsCollection(elements, options) {
    options = Object.assign({
        url: '',
        minToRequest: 1,
        justUse: 0,
        //onSelect: function(value, caption) { return true; },
        //onSuccess: function() {},
    }, options);

    var that = {
        attach: function (elements) {
            Iterable(elements).forEach(function (input) {
                if (input.getAttribute('data-multiple')) {
                    JsCollectionSelectorMultiple(input, options);
                } else {
                    JsCollectionSelector(input, options);
                }
            });

            return true;
        },
    };

    // Events handle
    options.fireEvent = function (eventName, context) {
        if (typeof this[eventName] == 'function') {
            return this[eventName].apply(context, arguments);
        }
    };

    that.attach(elements);

    return that;
}

function JsCollectionSelector(input, options) {
    var name = input.name;
    var wrapper = input.parentNode.parentNode;
    var xmark = wrapper.querySelector('button');
    var hidden = wrapper.querySelector('input[type=hidden]');
    var picker = JsCollectionPicker(wrapper, input, options, function (value, caption, justUse) {
        input.value = caption;
        input.name = justUse ? name : '';
        hidden.value = value;
        active(false);
    });

    function clear() {
        hidden.value = '';
        input.name = ''; // for justUse
    }

    function active(status) {
        if (status) {
            if (input.readOnly) {
                clear();
                input.value = '';
                input.readOnly = false;
                input.parentNode.removeChild(xmark);
                input.focus();
            }
        } else {
            input.readOnly = true;
            input.parentNode.appendChild(xmark);
        }
    }

    if (hidden.value) {
        active(false);
    } else {
        input.parentNode.removeChild(xmark);
    }

    // prepare input
    input.name = '';
    input.addEventListener('click', function () {
        active(true);
    });
    input.addEventListener('input', function () {
        clear();
        if (input.value.length < options.minToRequest) {
            return picker.hide();
        }
        JsCollectionEngine(input, picker, options);
    });
    input.addEventListener('reset', function () {
        active(true);
    });

    xmark.addEventListener('click', function () {
        active(true);
    });
}

function JsCollectionSelectorMultiple(input, options) {
    let value = JSON.parse(input.getAttribute('data-options'));
    let __name = input.name;
    let name = input.name.slice(2);
    input.name = '';

    let wrapper = input.parentNode.parentNode;
    let group = wrapper.querySelector('ul');
    let picker = JsCollectionPicker(wrapper, input, options, function (value, caption, justUse) {
        let tagName = justUse ? __name : name;

        if (justUse) {
            value = caption;
        }

        let html = '<div class="input-tag">' + caption
            + '<input type="hidden" name="' + tagName + '[]" value="' + value + '"/>'
            + '</div>'
            + '<a href="javascript:void(0)" class="input-tag" role="button" aria-labelledby="collection-delete-option"><i class="fa-solid fa-xmark"></i></a>';

        let tag = group.appendChild(JsElement('li.input-tag-group', { html: html }));
        tag.setAttribute('role', 'option');
        tag.querySelector('a').addEventListener('click', function () {
            tag.parentNode.removeChild(tag);
            options.fireEvent('onDelete', picker);
        });
        input.value = '';
    });
    //
    for (let i in value) {
        picker.create(i, value[i]);
    }

    input.addEventListener('input', function () {
        if (input.value.length < options.minToRequest) {
            return picker.hide();
        }
        JsCollectionEngine(input, picker, options);
    });
}

function JsCollectionPicker(wrapper, input, options, select) {
    let status;
    let selected = -1;
    let picker = wrapper.querySelector('div[role=listbox]');
    let body = document.querySelector('body');

    input.addEventListener('keydown', function (event) {
        if (event.key == 'Enter') {
            event.preventDefault();
            if (selected > -1) {
                picker.querySelectorAll('ul li')[selected].click();
            } else {
                input.click();
            }
        } else if (event.key == 'ArrowDown') {
            event.preventDefault();
            nav(true);
        } else if (event.key == 'ArrowUp') {
            event.preventDefault();
            nav(false);
        } else {
            selected = -1;
        }
    });

    function nav(forward) {
        if (status) {
            let li = picker.querySelectorAll('ul li');
            let total = li.length;

            selected += forward ? 1 : -1;
            if (selected < 0) {
                selected = 0;
            } else if (selected > (total - 1)) {
                selected = (total - 1);
            }
            li.forEach(function (el, i) {
                el.classList.toggle('active', i == selected);
            });
        }
    }

    function toggle(i) {
        status = Boolean(i);
        body[['removeEventListener', 'addEventListener'][i]]('click', picker.hide); // hide picker on document click
        picker.style.display = ['none', ''][i];
        input.setAttribute('aria-expanded', status);
    }

    return JsElement(picker, {
        events: {
            click: function (event) {
                event.stopPropagation();
            }
        },
        show: function () {
            toggle(1);
        },
        hide: function () {
            toggle(0);
        },
        select: function (value, caption, justUse) {
            if (options.fireEvent('onSelect', this, value, caption) === false) {
                return;
            }
            select(value, caption, justUse);
            this.hide();
            options.fireEvent('onSuccess', this);
        },
        create: select,
    });
}

/**
 * Engine
 *
 * @This is the search engine
 *
 * @json = {
 *  "rows": [["value", "caption", "details"], ...]	// the list
 *  "isAll": (boolean),								// Are all results
 *  "isTable": (boolean),							// Is the complete table regardless of the search performed
 * }
 *
 */
var JsCollectionEngine = (function () {
    var input, picker, options;
    var Libraries = [];
    var Tables = [];
    var RQ = {
        '[áàâä]': 'a',
        '[éèêë]': 'e',
        '[íìîï]': 'i',
        '[óòôö]': 'o',
        '[úùûü]': 'u',
        '[ñ]': 'n'
    };
    var QR = {
        'a': '[aáàâä]',
        'e': '[eéèêë]',
        'i': '[iíìîï]',
        'o': '[oóòôö]',
        'u': '[uúùûü]',
        'n': '[nñ]'
    };

    function _findL(q) {
        let i = q.length + 1;
        while (i--) {
            if (Libraries[i]) {
                let j = Libraries[i].length;
                while (j--) {
                    if (Libraries[i][j].q == q) {
                        return Libraries[i][j];
                    }
                }
            }
            q = q.substr(0, i - 1);
        }
        return false;
    }

    function getRegExp(re) {
        for (let i in QR) {
            re = re.replace(RegExp(i, 'gi'), QR[i]);
        }
        return RegExp(re, 'i');
    }

    function _findQ(Library, q) {
        let rows = [];
        let length = Library.rows.length;

        if (length) {
            let re = getRegExp(q);

            for (let j = 0; j < length; j++) {
                i = Library.rows[j];
                if (Tables[Library.iT].rows[i][1].search(re) != -1) {
                    rows.push(i);
                }
            }
        }
        // return Library
        return _saveL(q, Library.iT, rows);
    }

    function _callback(q, json) {
        json.isAll = json.isTable || json.isAll;

        // save table
        let iT = Tables.push({
            rows: json.rows,
            isAll: json.isAll,
        }) - 1;

        // assign entire table
        let rows = [];
        for (let i in json.rows) {
            rows[i] = i;
        }

        if (json.isTable) {
            _printL(_findQ(_saveL('', iT, rows), q));
        } else {
            _printL(_saveL(q, iT, rows));
        }
    }

    function _saveL(q, iT, rows) {
        let i = q.length;
        if (!Libraries[i]) {
            Libraries[i] = [];
        }

        let j = Libraries[i].push({
            q: q,
            iT: iT,
            rows: rows,
        }) - 1;

        return Libraries[i][j];
    }

    function _printL(Library) {
        let rows = [];
        let length = Library.rows.length;

        for (let i = 0; i < length; i++) {
            rows[i] = Tables[Library.iT].rows[Library.rows[i]];
        }

        render(rows, Library.q);
    }

    function renderLine(ul, value, dt, dd, ju) {
        let html = '<div class="dt">' + dt + '</div>';
        if (dd) {
            html += '<div class="dd">' + dd + '</div>';
        }

        let li = ul.appendChild(JsElement('li', { html: html }));
        if (ju) {
            li.classList.add('just-use');
            li.setAttribute('aria-labelledby', 'collection-just-use')
        }
        li.setAttribute('role', 'option');
        li.setAttribute('aria-label', dt);
        li.addEventListener('click', function () {
            picker.select(value, dt, ju);
        });
    }

    function render(rows, q) {
        let ju = options.justUse && input.value;
        let ul = JsElement('UL');

        rows.forEach(function (row) {
            renderLine(ul, row[0], row[1], row[2], false);

            if (ju && row[1].toLowerCase() == q) {
                ju = false;
            }
        });

        if (ju) {
            let value = input.value.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            renderLine(ul, -1, value, '', true);
        }
        if (ul.firstChild) {
            picker.innerHTML = '';
            picker.appendChild(ul);
            picker.show();
        } else {
            picker.hide();
        }
    }

    function normalize(q) {
        q = q.toLowerCase();
        for (i in RQ) {
            q = q.replace(RegExp(i, 'g'), RQ[i]);
        }
        return q;
    }

    // main function
    return function (i, p, o) {
        input = i;
        picker = p;
        options = o;
        var q = normalize(input.value);

        // Seeking any saved queries
        var Library = _findL(q);

        if (Library) {
            if (Library.q == q) {
                //console.log('<-- 1');
                return _printL(Library);
            } else if (Tables[Library.iT].isAll) {
                //console.log('<-- 2');
                return _printL(_findQ(Library, q));
            }
        }

        //console.log('<-- request');
        JsRequest.json({
            url: options.url,
            data: JsRequest.mergeData(options.data, { q: q }),
            //spinner: 1,
            onSuccess: function (json) {
                _callback(q, json);
            },
        });
    };
})();

