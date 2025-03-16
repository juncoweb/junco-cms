<?php

/**
 * Contact-widget
 */

return [
	/**
	 * General
	 */
	'load_resources' => true,
	'delivery' => [
		0 => [
			0 => 'basic'
		],
		1 => [
			0 => 'links',
			1 => 'map'
		]
	],

	/**
	 * Basic
	 */
	'show_title' => true,
	'address' => '',
	'phone' => '',
	'email' => 'contact@example.com',

	/**
	 * Links
	 */
	'links' => [
		'contact' => [
			'title' => 'Contact',
			'color' => '<!--#fff-->',
			'icon' => 'fa fa-envelope',
			'url' => '/contact'
		],
		'facebook' => [
			'title' => 'Facebook',
			'color' => '<!--#3b5998-->',
			'icon' => 'fab fa-facebook-f',
			'url' => 'https://www.facebook.com'
		],
		'instagram' => [
			'title' => 'Instagram',
			'color' => '<!--#405de6-->',
			'icon' => 'fab fa-instagram',
			'url' => 'https://instagram.com'
		],
		'youtube' => [
			'title' => 'YouTube',
			'color' => '<!--#ff0000-->',
			'icon' => 'fab fa-youtube',
			'url' => 'https://www.youtube.com'
		]
	],

	/**
	 * Map
	 */
	'map_show_title' => true,
	'map_code' => '',
];
