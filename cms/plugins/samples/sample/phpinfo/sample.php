<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

defined('IS_TEST') or die;

if (app('system')->isDemo()) {
	$html = '<div class="m-4">' . _t('This task is not allowed in demos.') . '</div>';
} else {
	// @see: http://php.net/manual/es/function.phpinfo.php
	ob_start();
	phpinfo();
	preg_match('%<style type="text/css">(.*?)</style>.*?(<body>.*</body>)%s', ob_get_clean(), $matches);

	$html = '<style type="text/css">' . change_styles($matches[1]) . '</style>' . "\n"
		. '<div class="phpinfodisplay table-responsive">'
		. $matches[2] . "\n"
		. '</div>' . "\n";
}

// template
$tpl = Template::get();
$tpl->content = $html;

return $tpl->response();



/**
 * Change styles for phpinfo function.
 *
 * @param string $styles
 * 
 */
function change_styles(string $styles): ?string
{
	$styles = preg_replace_callback('/((?:\.|\w|:)(?:.*?))\{(.*?)\}/', function ($match) {
		return '.phpinfodisplay ' . implode(', .phpinfodisplay', explode(',', $match[1])) . '{' . $match[2] . '}';
	}, $styles);

	return strtr($styles, [
		' body' => '',
		' :root' => '',
		'@media (prefers-color-scheme: dark)' => '[data-theme=dark]'
	]);
}
