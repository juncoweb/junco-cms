<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

if ($availables) {
	$html   = '';
	foreach ($availables as $key => $value) {
		$html .= '<li><a href="javascript:void(0)" control-install="language" data-value="' . $key . '">' . $value . '</a></li>';
	}
	$html = _t('Select') . '<ul>' . $html . '</ul>';
} else {
	$html = '<div class="empty-list">' . _t('Empty list') . '</div>';
}

// template
$tpl = Template::get('install');
$tpl->options(['hash' => 'index']);
$tpl->title(_t('Select language'));
$tpl->content = '<div class="panel"><div class="panel-body">' . $html . '</div></div>';

return $tpl->response();
