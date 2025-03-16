/* --- Theme ------------------------------------------ */
const JsTheme = function ($btn) {
	const storageName = 'prefers-color-scheme';
	const $icon = $btn.querySelector('i');
	const currentTheme = localStorage.getItem(storageName) || 'auto';

	function set(theme, className, title) {
		if (theme === 'auto') {
			theme = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches
				? 'dark'
				: 'light';
		}
		document.documentElement.setAttribute('data-theme', theme);
		$icon.className = className;
		$btn.setAttribute('title', title);
		$btn.setAttribute('aria-label', title);
	}

	$btn.parentNode.querySelectorAll('[data-value]').forEach(function (el) {
		const theme = el.getAttribute('data-value');
		const className = el.querySelector('i').className;
		const title = el.querySelector('span').textContent;

		if (currentTheme === theme) {
			set(theme, className, title);
		}

		el.addEventListener('click', function () {
			localStorage.setItem(storageName, theme);
			document.body.click();
			set(theme, className, title);
		});
	});
};