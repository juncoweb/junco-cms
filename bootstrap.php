<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

define('SYSTEM_ABSPATH', dirname(__FILE__) . '/');
define('SYSTEM_STORAGE', SYSTEM_ABSPATH . 'storage/');
define('SYSTEM_SETPATH', SYSTEM_STORAGE . 'settings/');
define('SYSTEM_MEDIA_PATH', 'media/');
define('SYSTEM_AUTOLOAD', 'cms/libraries/system/autoload.php');
define('GET', INPUT_GET);
define('POST', INPUT_POST);

# Redirect to install - Remove after installation.
$min_php_version = '8.2';
$max_php_version = '';

version_compare(PHP_VERSION, $min_php_version, '>=')
	or die("Requires PHP {$min_php_version} or above.");

$max_php_version
	and version_compare(PHP_VERSION, $max_php_version, '<=')
	or die("Requires PHP {$max_php_version} or lower.");

if (is_dir(SYSTEM_ABSPATH . 'app/install')) {
	$goto = explode('/', $_GET['goto'] ?? '')[0];

	if (!in_array($goto, ['console', 'install'])) {
		header('Location: ?goto=install');
		die;
	}
}
