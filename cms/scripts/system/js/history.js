
/* --- History ----------------------------------------------- */
var JsHistory = function(stateKey, fn, stateValue) {
	var
	w = window.top,
	h = w.history,
	d = w.document,
	l = d.location,
	state = h.state || {};
	_value = stateValue !== undefined ? stateValue : l.href;
	
	// replace state
	state[stateKey] = _value;
	h.replaceState(state, d.title, l.href);
	
	// event handler
	window.addEventListener('popstate', function(event) {
		//event.preventDefault();
		var v = event.state[stateKey];
		if (_value != v) {
			_value = v;
			fn(event);
		}
	});
	
	return {
		push: function(title, url, value) {
			if (typeof value == 'function') {
				value = value(h.state[stateKey]);
			} else if (!value) {
				value = url;
			}
			h.state[stateKey] = _value = value;
			h.pushState(
				h.state,
				title || '',
				url
			);
		},
	};
};

JsHistory.check = function() {
	return (history && history.pushState);
};
