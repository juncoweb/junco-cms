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
				'href' => 'https://use.fontawesome.com/releases/v6.2.1/css/all.css',
				'integrity' => 'sha384-twcuYPV86B3vvpwNhWJuaLdUSLF9+ttgM2A6M870UYXrOsxKfER2MKox5cirApyA',
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
