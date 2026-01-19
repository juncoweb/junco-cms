<?php

/**
 * Frontend
 */

return [
	/**
	 * General
	 */
	'default_options' => [
		'css' => [
			0 => [
				'rel' => 'stylesheet',
				'href' => 'https://use.fontawesome.com/releases/v7.1.0/css/all.css',
				'integrity' => 'sha384-YgSbYtJcfPnMV/aJ0UdQk84ctht/ckX0MrfQwxOhw43RMBw2WSaDSMVh4gQwLdE4',
				'crossorigin' => 'anonymous'
			],
			1 => 'assets/system.min.css'
		],
		'js' => [
			0 => 'assets/system-basic.min.js'
		]
	],
	'alter_options' => [
		'myspace' => [
			'js' => 'assets/system-advanced.min.js',
			'sidebar' => [
				0 => 'frontend.myspace'
			]
		]
	],
	'on_display' => '',

	/**
	 * Themes
	 */
	'theme' => '',
	'print_theme' => '',

	/**
	 * Header
	 */
	'header_style' => '',
	'header_fixed' => true,
	'logo_text' => '',
	'logo_img' => '',
	'topbar' => [
		0 => 'login',
		1 => 'theme',
		2 => 'language',
		3 => 'notifications',
		4 => 'search',
		5 => 'contact'
	],
	'navbar' => 'frontend.navbar',

	/**
	 * Sidebar
	 */
	'sidebar' => [
		0 => 'search'
	],

	/**
	 * Footer
	 */
	'footer_style' => '',
	'footer' => [
		0 => 'contact'
	],

	/**
	 * Copyright
	 */
	'copyright_style' => '',

	/**
	 * Legal
	 */
	'terms_url' => '',
	'privacy_url' => '',
	'cookie_consent' => 'frontend',
];
