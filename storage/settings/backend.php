<?php

/**
 * Backend
 */

return [
	/**
	 * General
	 */
	'theme' => '',
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
			0 => 'assets/system.min.js'
		]
	],

	/**
	 * Mainbar
	 */
	'header_color' => 'info',
	'mainbar' => [
		0 => 'backend.search',
		1 => 'backend.topbar'
	],

	/**
	 * Sidebar
	 */
	'sidebar' => [
		0 => 'backend.navbar'
	],
];
