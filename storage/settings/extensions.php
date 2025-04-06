<?php

/**
 * Extensions
 */

return [
	/**
	 * General
	 */
	'compiler_path' => 'updates/',
	'installer_path' => 'updates/',

	/**
	 * Version
	 */
	'min_php_version' => '8.2',
	'max_php_version' => '0',

	/**
	 * Paths
	 */
	'system_path' => 'system/',
	'sql_path' => 'sql/',
	'xdata_path' => 'xdata/',

	/**
	 * Files
	 */
	'install_file' => 'install.json',

	/**
	 * System
	 */
	'system_extra' => 'composer.json.dist,
index.php,
LICENSE,
maintenance.html,
README.md',
	'system_install_extra' => 'bootstrap.php,
favicon.ico,
htaccess,
assets/index.html,
cms/index.html,
cms/libraries/.htaccess,
cms/plugins/.htaccess,
cms/scripts/index.html,
cms/snippets/.htaccess,
app/index.html,
media/index.html,
storage/.htaccess,
storage/locale/,
storage/etc/users-labels.php,
vendor/.htaccess',

	/**
	 * Compiler
	 */
	'compiler_plugins' => [
	],
];
