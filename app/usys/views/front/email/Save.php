<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

if ($error) {
	$title = _t('Error Page');
	$html = $error_msg;
} else {
	$title = _t('Confirmation correct!');
	$html = '<p>' . _t('Your email has been confirmed and the changes have been saved.') . '</p>'
		. '<p>' . sprintf(_t('Sincerely %s'), config('site.name')) . '</p>';
}

// template
$tpl = Template::get();
$tpl->options($options);
$tpl->title($title, _t('Save Email'));
$tpl->content = $html;

return $tpl->response();
