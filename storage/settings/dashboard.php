<?php

/**
 * Dashboard
 */

return [
	/**
	 * Administration
	 */
	'admin_options' => [
		'js' => 'assets/dashboard-admin.min.js'
	],
	'admin_snippet' => 'default',
	'admin_plugins' => [
		0 => 'extensions',
		1 => 'dashboard.shortcuts',
		2 => 'users',
		3 => 'contact'
	],

	/**
	 * My space
	 */
	'myspace_options' => [
		'load' => 'myspace',
		'hash' => 'dashboard',
		'js' => 'assets/dashboard-admin.min.js'
	],
	'myspace_snippet' => 'default',
	'myspace_plugins' => '',
];
