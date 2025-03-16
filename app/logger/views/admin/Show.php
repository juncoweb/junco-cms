<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

$_context = '';
if ($context) {
	$session = session();
	foreach ($context as $name => $value) {
		if ($name == 'user_agent') {
			$browser = $session->getBrowser($value);
			$_context .= '<div class="badge badge-regular" title="' . $value . '">' . $name . ': ' . implode('/', $browser) . '</div>';
		} else {
			$_context .= '<div class="badge badge-regular">' . $name . ': ' . $value . '</div>';
		}
	}
}

$html = '<tr><th>' . _t('Level') . '</th><td class="text-uppercase">' . $level . '</td></tr>'
	. '<tr><th>' . _t('Message') . '</th><td>' . $message . '</td></tr>'
	. '<tr><th>' . _t('File') . '</th><td>' . $file . '</td></tr>'
	. '<tr><th>' . _t('Date') . '</th><td>' . $created_at->format(_t('Y-M-d')) . ' <span class="color-light">' . $created_at->format(_t('H:i:s')) . '</span>' . '</td></tr>'
	. '<tr><th>' . _t('Context') . '</th><td>' . ($_context ?: '-') . '</td></tr>'
	. '<tr><th>' . _t('Trace') . '</th><td><div>' . implode('</div><div>', $backtrace) . '</div></td></tr>';

// modal
$modal = Modal::get();
$modal->close();
$modal->title(_t('Info'));
$modal->content = '<div class="table-responsive"><table class="table table-bordered"><tbody>' . $html . '</tbody></table></div>';

return $modal->response();
