<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

defined('IS_TEST') or die;


$html = '';
$colors = ['default', 'primary', 'secondary', 'success', 'info', 'warning', 'danger'];

foreach ($colors as $color) {
	$html .= '<div class="toast toast-' . $color . '">'
		.   '<div class="toast-body">' . $color . '</div>'
		.   '<div class="toast-close"><i class="fa-solid fa-xmark"></i></div>'
		. '</div>';
}
$html = '<p><button class="btn btn-primary btn-solid" onclick="JsToast(\'Lorem ipsum\')" aria-label="Launches a toast alert">Toast</button></p><div style="width: 350px">' . $html . '</div>';

// template
$tpl = Template::get();
$tpl->options(['thirdbar' => 'system.thirdbar']);
$tpl->title('Toast');
$tpl->content = '<div class="panel"><div class="panel-body">' . $html . '</div></div>';

return $tpl->response();
