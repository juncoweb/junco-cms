
/**
 * Date
 *
 * @events:
 * onSelect
 * onShow
 * onHide
 *
 */
const JsDatepicker = (function () {
    function pad(number) {
        if (number < 10) {
            return '0' + number;
        }
        return number;
    }

    function toLocaleDateString(date, withTime) {
        let result = date.toLocaleDateString();

        if (withTime) {
            result += ' ' + date.toLocaleTimeString().replace(/^(\d{1,2}:\d{1,2}):\d{1,2}([^\d]*)$/, '$1$2');
        }

        return result;
    }

    function toInputDateString(date, withTime) {
        let result = date.getFullYear()
            + '-' + pad(date.getMonth() + 1)
            + '-' + pad(date.getDate());

        if (withTime) {
            result += 'T' + pad(date.getHours()) + ':' + pad(date.getUTCMinutes());
        }

        return result;
    }

    function newDate(textDate) {
        const match = textDate.match(/^\d{4}-\d{2}-\d{2}(T\d{2}:\d{2}(:\d{2})?)?$/);

        if (!match) {
            return;
        } else if (match[1] == undefined) {
            textDate += 'T00:00:00';
        } else if (match[2] == undefined) {
            textDate += ':00';
        }

        return new Date(textDate);
    }

    let Locale = null;
    function getLocale(language) {
        const date = new Date();
        const locale = {
            language,
            firstDayOfWeek: new Intl.Locale(language)?.weekInfo?.firstDay ?? 0,
            months: [],
            shortMonths: [],
            shortWeekdays: []
        };

        for (let i = 0; i < 12; i++) {
            date.setMonth(i);
            locale.months[i] = date.toLocaleString(language, { month: 'long' });
            locale.shortMonths[i] = date.toLocaleString(language, { month: 'short' });
        }

        date.setFullYear(2017, 9); // year and month whose first day falls on a Monday
        for (let i = 0; i < 7; i++) {
            date.setDate(i + 1);
            locale.shortWeekdays[i] = date.toLocaleString(language, { weekday: 'narrow' }); //'short'
        }

        return locale;
    }

    function MarksList() {
        const marksList = {};

        return {
            store: function (year, month, day) {
                marksList[year] ??= {};
                marksList[year][month] ??= {};

                if (day !== undefined) {
                    marksList[year][month][day] = true;
                }
            },

            storeAll: function (list) {
                if (Array.isArray(list)) {
                    list.forEach((date) => {
                        date = newDate(date);
                        if (date) {
                            this.store(date.getFullYear(), date.getMonth(), date.getDate());
                        }
                    });
                }

                return this;
            },

            has: function (year, month, day) {
                if (year === undefined) {
                    return Object.keys(marksList).length === 0;
                }
                if (!marksList[year]) {
                    return false;
                }
                if (month === undefined) {
                    return true;
                }
                if (!marksList[year][month]) {
                    return false;
                }
                if (day === undefined) {
                    return true;
                }
                return marksList[year][month][day];
            },
        };
    }

    function Marks(options) {
        options = Object.assign({
            list: null,
            url: null,
            data: {},
            mode: 'month', // month, year
        }, options || {});

        const marksList = MarksList().storeAll(options.list);

        function print(cells, year, month) {
            for (let i = 0; i < 42; i++) {
                if (marksList.has(year, month, day(cells[i]))) {
                    cells[i].classList.add('marked');
                }
            }
        }

        function day(cell) {
            return cell.classList.contains('ext')
                ? -1
                : cell.innerHTML;
        }

        function has(year, month) {
            return options.mode == 'month'
                ? marksList.has(year, month)
                : marksList.has(year);
        }

        function async(cells, year, month) {
            if (has(year, month)) {
                print(cells, year, month);
            } else {
                marksList.store(year, month);
                JsRequest.json({
                    url: options.url,
                    data: [options.data, { year, month }],
                    onSuccess: function (json) {
                        marksList.storeAll(json.list);
                        print(cells, year, month);
                    }
                });
            }
        }

        return {
            load: function (cells, year, month) {
                if (options.url) {
                    async(cells, year, month);
                } else if (marksList.has(year, month)) {
                    print(cells, year, month);
                }
            }
        };
    }

    function Picker(options) {
        Locale ??= getLocale(navigator.language); // "ko-KR"; "ar"; "ja-Kanaar";
        let date;
        let selected;
        const _panels = {};
        const icons = [
            'fa-solid fa-chevron-left',
            'fa-solid fa-circle',
            'fa-solid fa-chevron-right',
            'fa-solid fa-chevron-up',
            'fa-solid fa-chevron-down',
            'fa-regular fa-clock',
        ];

        function setSelected() {
            selected = new Date(date.getTime());
        }

        function select() {
            setSelected();
            options.fire('onSelect', date);
        }

        function builder(selector, cols, rows = 1) {
            let thead = '';
            let tbody = '';

            if (typeof rows == 'object') {
                rows.forEach(function (v) {
                    thead += '<th>' + v + '</th>';
                });

                rows = rows.length - 1;
                thead = '<thead><tr>' + thead + '</tr></thead>';
            }

            for (let i = 0, L = rows * cols; i < L; i++) {
                tbody += (!(i % cols) && i ? '</tr><tr>' : '') + '<td></td>';
            }

            picker.querySelector(selector).innerHTML = '<table>'
                + thead
                + '<tbody><tr>' + tbody + '</tr></tbody>'
                + '</table>';

            const cells = Array.from(picker.querySelectorAll(selector + ' table tbody tr td'));

            return {
                click: function (fn) {
                    cells.forEach(function (el, i) {
                        el.addEventListener('click', function () {
                            fn(el, i);
                        });
                    });

                    return this;
                },

                icon: function (...value) {
                    cells.forEach(function (el, i) {
                        if (value[i] == -1) {
                            return;
                        }
                        el.innerHTML = '<i class="' + icons[value[i]] + '"></i>';
                    });

                    return this;
                },

                html: function (fn) {
                    cells.forEach(function (el, i) {
                        el.innerHTML = fn(i);
                    });

                    return this;
                },

                set: function (methods) {
                    for (let i in methods) {
                        _panels[i] = methods[i].bind({ cells: cells });
                    }

                    return this;
                },
            };
        }

        function toggle(opt) {
            if (opt && picker.classList.contains('x' + opt)) {
                opt = 0;
            }

            switch (opt) {
                case 0:
                    _panels.updateDays();
                    break;

                case 1:
                    _panels.updateMonths();
                    break;

                case 2:
                    _panels.updateYears();
                    break;

                case 3:
                    _panels.updateHours();
                    _panels.updateMinutes();
                    break;
            }

            picker.className = 'input-datepicker x' + opt;
        }

        function getWeekdays() {
            const weekdays = [];

            for (let i = 0; i < 7; i++) {
                weekdays.push(Locale.shortWeekdays[(Locale.firstDayOfWeek + i) % 7]);
            }

            return weekdays;
        }

        // picker
        const picker = JsElement('div.input-datepicker', {
            html: '<div class="header"></div>'
                + '<div class="s-days"><div class="days"></div><div class="nav"></div></div>'
                + '<div class="s-months"></div>'
                + '<div class="s-years"><div class="years"></div><div class="nav"></div></div>'
                + (options.withTime ? '<div class="s-time"></div><div class="s-hours"></div><div class="s-minutes"></div>' : '')
        });

        picker.setStyles = function (styles) {
            for (let i in styles) {
                this.style[i] = styles[i];
            }
        };

        picker.load = function (value) {
            date = value || new Date();
            setSelected();
            toggle(0);
        };

        // header
        builder('.header', options.withTime ? 3 : 2)
            .click(function (el, i) {
                toggle(i + 1);
            })
            .set({
                updateHeader: function (year, month = -1) {
                    this.cells[1].innerHTML = year;
                    if (month > -1) {
                        this.cells[0].innerHTML = Locale.months[month];
                    }
                }
            })
            .icon(-1, -1, 5);

        // days nav
        builder('.s-days .nav', 3)
            .click(function (el, i) {
                _panels.updateDays(i - 3);
            })
            .icon(0, 1, 2);


        // days
        builder('.days', 7, getWeekdays())
            .click(function (el) {
                const day = el.innerHTML;
                const month = el.className == 'ext'
                    ? (day < 15 ? -1 : -3)
                    : undefined;

                _panels.updateDays(month, day);
                select();
                _panels.updateDays();
            })
            .set({
                updateDays: function (monthCmd, dayCmd) {
                    const today = new Date();

                    if (monthCmd != undefined) {
                        switch (monthCmd) {
                            case -2: // today
                                date.setFullYear(today.getFullYear(), today.getMonth(), today.getDate());
                                break;
                            case -3: // prev and next month
                            case -1:
                                monthCmd += 2 + date.getMonth();
                            // break;
                            default:
                                date.setMonth(monthCmd);
                                break;
                        }
                    }
                    if (dayCmd != undefined) {
                        date.setDate(dayCmd);
                    }

                    const month = date.getMonth();
                    const year = date.getFullYear();
                    const firstDay = (7 - Locale.firstDayOfWeek + (new Date(year, month, 1).getDay())) % 7; // Position of first day
                    const lastDay = new Date(year, month + 1, 0).getDate(); // Last day of the month
                    let prevDays = new Date(year, month, 0).getDate() - firstDay; // I calculate the visible days of the previous month.
                    let day = nextDays = 0;

                    function cell(el, i, css = '') {
                        el.innerHTML = i;
                        el.className = css;
                    }

                    for (let i = 0; i < 42; i++) {
                        if (i >= firstDay && day < lastDay) { // In month
                            cell(this.cells[i], ++day);
                        } else { // padding
                            cell(this.cells[i], day ? ++nextDays : ++prevDays, 'ext');
                        }
                    }

                    if (today.getFullYear() == year
                        && today.getMonth() == month
                    ) {
                        this.cells[today.getDate() + firstDay - 1].classList.add('today');
                    }

                    // selected
                    if (selected
                        && selected.getFullYear() == year
                        && selected.getMonth() == month
                    ) {
                        this.cells[selected.getDate() + firstDay - 1].classList.add('selected');
                    }

                    _panels.updateHeader(year, month);
                    options.marks.load(this.cells, year, month);
                }
            });

        // months
        builder('.s-months', 4, 3)
            .click(function (el, i) {
                _panels.updateDays(i);
                toggle(0);
            })
            .html(function (i) {
                return Locale.shortMonths[i];
            })
            .set({
                updateMonths: function () {
                    const month = date.getMonth();
                    let i = 12;

                    while (i--) {
                        this.cells[i].className = (i == month ? 'selected' : '');
                    }
                }
            });

        // years nav
        builder('.s-years .nav', 2)
            .click(function (el, i) {
                _panels.updateYears(i ? 1 : -1);
            })
            .icon(0, 2);

        // years
        builder('.years', 3, 5)
            .click(function (el) {
                date.setFullYear(el.innerHTML);
                _panels.updateDays();
                toggle(1);
            })
            .set({
                updateYears: function (page) {
                    let i = 15;
                    let year = date.getFullYear();

                    if (page) {
                        year += (page * i);
                        date.setFullYear(year);
                    }

                    while (i--) {
                        this.cells[i].innerHTML = year + i - 7;
                        this.cells[i].className = (i == 7 ? 'selected' : '');
                    };

                    _panels.updateHeader(year);
                }
            });

        if (options.withTime) {
            // time
            builder('.s-time', 2, 3)
                .click(function (el, i) {
                    switch (i) {
                        case 0: _panels.updateHours(1); break;
                        case 1: _panels.updateMinutes(1); break;
                        case 2: toggle(4); break;
                        case 3: toggle(5); break;
                        case 4: _panels.updateHours(-1); break;
                        case 5: _panels.updateMinutes(-1); break;
                    }
                })
                .icon(3, 3, -1, -1, 4, 4)
                .set({
                    updateHours: function (v) {
                        if (v) {
                            v += date.getHours();
                            date.setHours(v > 23 ? 0 : (v < 0 ? 23 : v));
                        }
                        this.cells[2].innerHTML = pad(date.getHours());
                        select();
                    },
                    updateMinutes: function (v) {
                        if (v) {
                            v += date.getMinutes();
                            date.setMinutes(v > 59 ? 0 : (v < 0 ? 59 : v));
                        }
                        this.cells[3].innerHTML = pad(date.getMinutes());
                        select();
                    }
                });

            // hours
            builder('.s-hours', 4, 6)
                .click(function (el, i) {
                    date.setHours(i);
                    toggle(3);
                    _panels.updateHours();
                })
                .html(function (i) {
                    return pad(i);
                });

            // minutes
            builder('.s-minutes', 3, 4)
                .click(function (el, i) {
                    date.setMinutes(i * 5);
                    toggle(3);
                    _panels.updateMinutes();
                })
                .html(function (i) {
                    return pad(i * 5);
                });
        }

        return picker;
    }

    function enhanceInput(element, options) {
        const $hidden = element.parentNode.insertBefore(JsElement('INPUT', {
            type: 'hidden',
            name: element.name
        }), element);

        element.name = '';
        element.setAttribute('readonly', true);
        element.getValue = function () {
            return $hidden.value
                ? newDate($hidden.value)
                : false;
        };
        element.setValue = function (value) {
            if (value == 'next day') {
                value = this.getValue();
                value.setDate(value.getDate() + 1);
            } else if (value == 'prev day') {
                value = this.getValue();
                value.setDate(value.getDate() - 1);
            }

            this.value = toLocaleDateString(value, options.withTime);
            $hidden.value = toInputDateString(value, options.withTime);
        };

        if (element.value) {
            const ct = newDate(element.value);
            if (ct) {
                element.setValue(ct);
            } else {
                element.value = '';
            }
        }
    }

    function enhanceElement(element, options) {
        element.getValue = function () {
            return this.getAttribute('data-value');
        };
        element.setValue = function (value) {
            if (value == 'next day') {
                value = this.getValue();
                value.setDate(value.getDate() + 1);
            } else if (value == 'prev day') {
                value = this.getValue();
                value.setDate(value.getDate() - 1);
            }
            this.setAttribute('data-value', toInputDateString(value, options.withTime));
        };
    }

    function Dropdown(_picker, element, options) {
        let displayTime = 0;

        function isActive() {
            return _picker.style.display == 'block' && Date.now() - displayTime > 200;
        }

        function position() {
            const rect = element.getBoundingClientRect();
            const doc = document.documentElement;

            _picker.setStyles({
                position: 'absolute',
                top: (rect.top + rect.height + doc.scrollTop + options.offset.y) + 'px',
                left: (rect.left + doc.scrollLeft + options.offset.x) + 'px',
                'z-index': 1000
            });
        }

        return {
            show: function () {
                displayTime = Date.now();
                if (!options.inline) {
                    position();
                }
                _picker.setStyles({ display: 'block' });
            },

            hide: function () {
                _picker.setStyles({
                    display: 'none',
                    position: '',
                    top: '',
                    left: '',
                });
            },

            load: function (show, hide) {
                this.hide();
                element.addEventListener('focus', show);
                document.querySelector('html').addEventListener('click', function () {
                    isActive() && hide();
                });
                _picker.addEventListener('click', function (event) {
                    event.stopPropagation();
                    return false;
                });
            }
        }
    }

    return function (element, options) {
        options = Object.assign({
            type: 'date',
            inject: null,
            dropdown: true,
            inline: false,
            offset: { x: 0, y: -3 },
            marks: null,
            //onShow: function() {},
            //onHide: function() {},
            onSelect: function (date) {
                this.setValue(date);
            },
            fire: function (name, ...args) {
                if (typeof this[name] == 'function') {
                    return this[name].apply(that, args);
                }
            }
        }, options);

        options.marks = Marks(options.marks);
        options.withTime = (options.type == 'datetime-local') ? 1 : 0;

        if (typeof element == 'string') {
            element = document.querySelector(element);
        }
        if (typeof options.inject == 'string') {
            options.inject = document.querySelector(options.inject);
        }

        if (element.tagName == 'INPUT') {
            enhanceInput(element, options);
        } else {
            enhanceElement(element, options);

            if (!options.inject) {
                options.inject = element;
            }
        }

        const _picker = Picker(options);
        const _dropdown = Dropdown(_picker, element, options);
        const that = {
            show: function () {
                _picker.load(element.getValue());
                _dropdown.show();
                options.fire('onShow');
                return this;
            },

            hide: function () {
                _dropdown.hide();
                options.fire('onHide');
                return this;
            },

            setValue: function (date) {
                element.setValue(date);
                return this;
            }
        };

        if (options.dropdown) {
            _dropdown.load(
                () => that.show(),
                () => that.hide()
            );
        } else {
            _picker.load();
        }

        (options.inject || document.body).appendChild(_picker);

        return that;
    };
})();

// felem implement
JsFelem.implement({
    'date': function (el) {
        JsDatepicker(el);
    },

    'datetime-local': function (el) {
        JsDatepicker(el, { type: 'datetime-local' });
    }
});

JsFelem.observe({
    'input[type=date]': function (el) {
        if (el.type != 'date') {
            JsDatepicker(el, { type: 'date' });
        }
    },
    'input[type=datetime-local]': function (el) {
        if (el.type != 'datetime-local') {
            JsDatepicker(el, { type: 'datetime-local' });
        }
    }
});
