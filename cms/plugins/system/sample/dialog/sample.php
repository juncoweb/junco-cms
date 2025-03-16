<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

defined('IS_TEST') or die;

// vars
$colors = ['default', 'primary', 'secondary', 'success', 'info', 'warning', 'danger'];
$html = '';

foreach ($colors as $color) {
	$html .= '<div class="dialog' . ($color ? ' dialog-' . $color : '') . '">' . $color . '</div>';
}

// template
$tpl = Template::get();
$tpl->options([
	'thirdbar' => 'system.thirdbar'
]);
$tpl->title('Dialog');
$tpl->content = '<div class="panel"><div class="panel-body">' . $html . '</div></div>';

return $tpl->response();
