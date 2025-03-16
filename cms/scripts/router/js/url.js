
/**
 * Url
 */
var JsUrl = (function() {
	var options = JsOptions('url').get({
		useRewrite: false,
		setLang: false,
		lang: 'lang',
		go: 'goto',
		format: 'format',
	});

	function toQueryString(args) {
		let arr = [];
		for (let name in args) {
			arr.push(name + '=' + encodeURIComponent(args[name]));
		}
		return arr.join('&');
	}

	//
	let lang = options.setLang ? document.querySelector('html').getAttribute('lang') : false;
	if (lang) {
		lang = lang.replace('-', '_');
	}

	// return function
	return function(route, args, format) {
		route = route.split('/');
		args  = Object.assign({}, args);

		let access_point = route[0];
		let component	= route[1] || '';
		let task		= route[2] || '';
		let separator 	= '?';
		let url;
		
		if (access_point == 'front') { // hack
			access_point = '';
		}

		if (options.useRewrite) {
			url = [];
			
			// URL language mode
			if (lang) {
				url.push(lang);
			}

			if (access_point) {
				url.push(access_point);
			}
			
			if (component) {
				url.push(component);
			}
			
			if (task) {
				url.push(task);
			}

			url = url.join('/');
		
		} else {
			url = 'index.php';
			let go = [];
			let _args = {};
			
			if (access_point) {
				go.push(access_point);
			}

			if (component) {
				go.push(component);
			}
			
			if (task) {
				go.push(task);
			}
			
			if (go.length) {
				_args[options.go] = go.join('/');
			}

			// URL language mode
			if (lang) {
				_args[options.lang] = lang;
			}
			
			if (Object.keys(_args).length) {
				url += separator + toQueryString(_args);
				separator = '&';
			}
		}

		if (format) {
			args[options.format] = format;
		}

		if (Object.keys(args).length) {
			url += separator + toQueryString(args);
		}

		return url;
	};
})();
