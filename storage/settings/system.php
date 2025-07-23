<?php

/**
 * System
 */

return [
	/**
	 * General
	 */
	'developer_mode' => false,
	'profiler' => '0',
	'handle_errors' => '',

	/**
	 * Statement
	 */
	'statement' => 10,

	/**
	 * Locale
	 */
	'timezone' => 'America/Argentina/Buenos_Aires',

	/**
	 * Editor
	 */
	'default_editor' => 'tinymce',

	/**
	 * Cache
	 */
	'allow_cache' => '',
	'cache_short_ttl' => 3600,
	'cache_long_ttl' => 86400,

	/**
	 * Cookies
	 */
	'cookie_lifetime' => 0,
	'cookie_path' => '/',
	'cookie_domain' => '',
	'cookie_secure' => 0,
	'cookie_httponly' => 0,

	/**
	 * Paths
	 */
	'mkdir_mode' => 493,
	'cgi_bin' => 'cgi-bin/',
	'log_path' => 'logs/',
	'tmp_path' => 'tmp/',

	/**
	 * Snippets
	 */
	'default_snippets' => [
		'pagination' => [
			'my' => [
				0 => 'backlist',
				1 => 'simple'
			],
			'admin' => [
				0 => 'backlist',
				1 => 'default'
			]
		]
	],
];
