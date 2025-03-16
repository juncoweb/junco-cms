<?php

/**
 * Assets
 */

return [
	/**
	 * General
	 */
	'precompile' => 2,
	'version_control' => true,

	/**
	 * Assets
	 */
	'data_path' => 'assets/data/',
	'minify_file' => 'assets/%s.min.%s',

	/**
	 * Themes
	 */
	'themes_path' => 'assets/themes/',

	/**
	 * Minifier
	 */
	'minify' => true,
	'fixurl' => 0,
	'cssmin_plugin' => 'assets',
	'jsmin_plugin' => 'assets',
];
