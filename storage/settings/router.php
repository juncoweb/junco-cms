<?php

/**
 * Router
 */

return [
	/**
	 * General
	 */
	'use_rewrite' => '',
	'access_points' => [
		0 => 'admin',
		1 => 'front',
		2 => 'audit',
		3 => 'my',
		4 => 'api',
		5 => 'console'
	],

	/**
	 * Keys
	 */
	'route_key' => 'goto',
	'format_key' => 'format',

	/**
	 * Front
	 */
	'front_default_component' => 'system',
	'route_replaces' => [
		'component' => [
			'contents' => 'blogs'
		],
		'task' => [
		]
	],

	/**
	 * Middlewares
	 */
	'middlewares' => [
		'admin' => [
			'authentication' => [
				0 => 'L_SYSTEM_ADMIN'
			]
		]
	],
];
