
/**
 * DateTime
 *
 * @events:
 * onSelect
 * onShow
 * onHide
 *
 */
var FeDate = (function () {

    // picker
    var Picker = (function () {
        var Locale = null;

        return function (fn, withTime) {
            var date;
            var selected;
            var firstDayOfWeek = 1;
            var _panels = {};
            var icons = [
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
                fn(date);
                return this;
            }

            function builder(selector, cols, rows = 1) {
                var thead = '';
                var tbody = '';

                if (typeof rows == 'object') {
                    for (var i = 0, L = rows.length; i < L; i++) {
                        thead += '<th>' + rows[i] + '</th>';
                    }

                    rows = L;
                    thead = '<thead><tr>' + thead + '</tr></thead>';
                }

                for (var i = 0, L = rows * cols; i < L; i++) {
                    tbody += (!(i % cols) && i ? '</tr><tr>' : '') + '<td></td>';
                }

                picker.querySelector(selector).innerHTML = '<table>'
                    + thead
                    + '<tbody><tr>' + tbody + '</tr></tbody>'
                    + '</table>';

                var grids = Array.from(picker.querySelectorAll(selector + ' table tbody tr td'));

                // that
                var that = {
                    click: function (fn) {
                        grids.forEach(function (el, i) {
                            el.addEventListener('click', function () {
                                fn(el, i);
                            });
                        });

                        return that;
                    },

                    icon: function (v) {
                        grids.forEach(function (el, i) {
                            if (v[i]) {
                                el.innerHTML = '<i class="' + icons[v[i] - 1] + '"></i>';
                            }
                        });

                        return that;
                    },

                    html: function (fn) {
                        grids.forEach(function (el, i) {
                            el.innerHTML = fn(i);
                        });

                        return that;
                    },

                    set: function (methods) {
                        for (var i in methods) {
                            _panels[i] = methods[i].bind({ grids: grids });
                        }

                        return that;
                    },
                };

                return that;
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

                picker.className = 'fe-date x' + opt;
            }


            // Locale
            if (!Locale) {
                Locale = { months: [], shortMonths: [], shortWeekdays: [] };
                var auxDate = new Date();

                for (var i = 0; i < 12; i++) {
                    auxDate.setMonth(i);
                    Locale.months[i] = auxDate.toLocaleString(undefined, { month: 'long' });
                    Locale.shortMonths[i] = auxDate.toLocaleString(undefined, { month: 'short' });
                }

                for (var i = 0; i < 7; i++) {
                    Locale.shortWeekdays[i] = new Date(2017, 9, i + 1).toLocaleString(undefined, { weekday: 'short' });
                }
            }

            // picker
            var picker = JsElement('div.fe-date', {
                html: '<div class="header"></div>'
                    + '<div class="s-days"><div class="days"></div><div class="nav"></div></div>'
                    + '<div class="s-months"></div>'
                    + '<div class="s-years"><div class="years"></div><div class="nav"></div></div>'
                    + (withTime ? '<div class="s-time"></div><div class="s-hours"></div><div class="s-minutes"></div>' : '')
            });

            picker.setStyles = function (styles) {
                for (var i in styles) {
                    this.style[i] = styles[i];
                }
            };

            picker.load = function (v) {
                date = v || new Date();
                setSelected();
                toggle(0);
            };

            // header
            builder('.header', withTime ? 3 : 2)
                .click(function (el, i) {
                    toggle(i + 1);
                })
                .set({
                    updateHeader: function (year, month = -1) {
                        this.grids[1].innerHTML = year;
                        if (month > -1) {
                            this.grids[0].innerHTML = Locale.months[month];
                        }
                    }
                })
                .icon([0, 0, 6]);

            // days nav
            builder('.s-days .nav', 3)
                .click(function (el, i) {
                    _panels.updateDays(i - 3);
                })
                .icon([1, 2, 3]);


            // days
            var rows = [];

            for (var i = 0; i < 7; i++) {
                rows.push(Locale.shortWeekdays[(firstDayOfWeek + i) % 7]);
            }

            builder('.days', 7, rows)
                .click(function (el) {
                    var month;
                    var day = el.innerHTML;

                    if (el.className == 'ext') {
                        month = day < 15 ? -1 : -3;
                    }

                    _panels.updateDays(month, day);
                    select();
                    _panels.updateDays();
                })
                .set({
                    updateDays: function (monthCmd, dayCmd) {
                        if (monthCmd != undefined) {
                            switch (monthCmd) {
                                case -2: // today
                                    var curDate = new Date();
                                    date.setFullYear(curDate.getFullYear(), curDate.getMonth(), curDate.getDate());
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

                        var month = date.getMonth();
                        var year = date.getFullYear();
                        var firstDay = (7 - firstDayOfWeek + (new Date(year, month, 1).getDay())) % 7; // Position of first day
                        var lastDay = new Date(year, month + 1, 0).getDate(); // Last day of the month
                        var prevDays = new Date(year, month, 0).getDate() - firstDay; // I calculate the visible days of the previous month.

                        //
                        var day = nextDays = 0;
                        var fn = function (el, i, css) {
                            el.innerHTML = i;
                            el.className = css;
                        };

                        for (var i = 0; i < 42; i++) {
                            if (i >= firstDay && day < lastDay) { // In month
                                fn(this.grids[i], ++day, '');
                            } else { // padding
                                fn(this.grids[i], day ? ++nextDays : ++prevDays, 'ext');
                            }
                        }

                        // today
                        var curDate = new Date();

                        if (curDate.getFullYear() == year
                            && curDate.getMonth() == month
                        ) {
                            this.grids[curDate.getDate() + firstDay - 1].classList.add('today');
                        }

                        // selected
                        if (selected
                            && selected.getFullYear() == year
                            && selected.getMonth() == month
                        ) {
                            this.grids[selected.getDate() + firstDay - 1].classList.add('selected');
                        }

                        _panels.updateHeader(year, month)
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
                        var i = 12;
                        var month = date.getMonth();

                        while (i--) {
                            this.grids[i].className = (i == month ? 'selected' : '');
                        }
                    }
                });

            // years nav
            builder('.s-years .nav', 2)
                .click(function (el, i) {
                    _panels.updateYears(i ? 1 : -1);
                })
                .icon([1, 3]);

            // years
            builder('.years', 3, 5)
                .click(function (el) {
                    date.setFullYear(el.innerHTML);
                    _panels.updateDays();
                    toggle(1);
                })
                .set({
                    updateYears: function (page) {
                        var i = 15;
                        var year = date.getFullYear();

                        if (page) {
                            year += (page * i);
                            date.setFullYear(year);
                        }

                        while (i--) {
                            this.grids[i].innerHTML = year + i - 7;
                            this.grids[i].className = (i == 7 ? 'selected' : '');
                        };

                        _panels.updateHeader(year);
                    }
                });

            if (withTime) {
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
                    .icon([4, 4, 0, 0, 5, 5])
                    .set({
                        updateHours: function (v) {
                            if (v) {
                                v += date.getHours();
                                date.setHours(v > 23 ? 0 : (v < 0 ? 23 : v));
                            }
                            this.grids[2].innerHTML = pad(date.getHours());
                            select();
                        },
                        updateMinutes: function (v) {
                            if (v) {
                                v += date.getMinutes();
                                date.setMinutes(v > 59 ? 0 : (v < 0 ? 59 : v));
                            }
                            this.grids[3].innerHTML = pad(date.getMinutes());
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
        };
    })();

    function pad(number) {
        if (number < 10) {
            return '0' + number;
        }
        return number;
    }

    function newDate(textDate) {
        var match = textDate.match(/^\d{4}-\d{2}-\d{2}(T\d{2}:\d{2}(:\d{2})?)?$/);

        if (!match) {
            return;
        } else if (match[1] == undefined) {
            textDate += 'T00:00:00';
        } else if (match[2] == undefined) {
            textDate += ':00';
        }

        return new Date(textDate);
    };

    return function (elements, options) {
        var _picker, _curElement;
        var that = {
            attach: function (elements) {
                elements = Iterable(elements);

                var i = elements.length;
                while (i--) (function (el) {
                    var name = el.name;
                    el.name = '';
                    el.setAttribute('readonly', true);
                    var H = el.parentNode.insertBefore(JsElement('INPUT', { 'type': 'hidden', 'name': name }), el);

                    el.getHDate = function () { return H.value ? newDate(H.value) : false; };
                    el.setHDate = function (v) {
                        //console.log(el.name +' = '+ that.options.type);
                        this.value = v.toLocaleDateString()
                            + (options.type ? ' ' + v.toLocaleTimeString().replace(/^(\d{1,2}:\d{1,2}):\d{1,2}([^\d]*)$/, '$1$2') : '');
                        H.value = v.getFullYear()
                            + '-' + pad(v.getMonth() + 1)
                            + '-' + pad(v.getDate())
                            + (options.type ? 'T' + pad(v.getHours()) + ':' + pad(v.getUTCMinutes()) : '');
                    };

                    if (el.value) {
                        var ct = newDate(el.value);
                        if (ct) {
                            el.setHDate(ct);
                        } else {
                            el.value = '';
                        }
                    }

                    if (options.setDrop) { // Display the datepicker on focus/click
                        el.addEventListener('focus', function () { that.show(el); });
                        //el.addEventListener('click', function(event) { event.stopPropagation(); });
                    }
                })(elements[i]);
            },

            /*
             * Display the datepicker
             */
            show: function (el) {
                _curElement = el; // Set the active element
                this.displayTime = Date.now();

                if (options.setPosition && _curElement) {
                    var rect = _curElement.getBoundingClientRect();
                    _picker.setStyles({
                        position: 'absolute',
                        top: (rect.top + rect.height + 3) + 'px',
                        left: rect.left + 'px',
                        'z-index': 1000
                    });
                }

                _picker.load(_curElement ? _curElement.getHDate() : false);
                _picker.setStyles({ display: 'block' });
                this.fireEvent('onShow');
                return this;
            },

            hide: function () {
                _picker.setStyles({
                    display: 'none',
                    position: '',
                    top: '',
                    left: '',
                });
                this.fireEvent('onHide');
                _curElement = null;
                return this;
            },

            fireEvent: function (name, param) {
                if (typeof options[name] == 'function') {
                    this[name] = options[name];
                    this[name](param);
                }
            },
        };

        // set options
        options = Object.assign({
            inject: null,
            setPosition: 1, // mixed - (boolean or object{x:number, y:number})
            setDrop: 1,
            offset: { x: 5, y: -3 },
            type: 0,

            // events
            onSelect: function (date) {
                _curElement && _curElement.setHDate(date);
            },
            //onShow: function() {},
            //onHide: function() {},
        }, options);

        options.type = options.type == 'datetime-local' ? 1 : 0;

        if (typeof options.inject == 'string') {
            options.inject = document.querySelector(options.inject);
        }

        that.attach(elements);

        _picker = new Picker(options.onSelect.bind(that), options.type);


        // Display the datepicker on focus/click
        if (options.setDrop) {
            document.querySelector('html').addEventListener('click', function () {
                if (_picker.style.display == 'block'
                    && Date.now() - that.displayTime > 200
                ) {
                    that.hide();
                }
            });

            _picker.setStyles({ display: 'none' });
            _picker.addEventListener('click', function (event) {
                event.stopPropagation();
                return false;
            });
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
        FeDate(el);
    },

    'datetime-local': function (el) {
        FeDate(el, { 'type': 'datetime-local' });
    }
});

JsFelem.observe({
    'input[type=date]': function (el) {
        if (el.type != 'date') {
            FeDate(el, { 'type': 'date' });
        }
    },
    'input[type=datetime-local]': function (el) {
        if (el.type != 'datetime-local') {
            FeDate(el, { 'type': 'datetime-local' });
        }
    }
});
