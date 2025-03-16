<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

defined('IS_TEST') or die;

// vars
$colors = ['default', 'primary', 'secondary', 'success', 'info', 'warning', 'danger'];
$html = '';

// ---
$partial = '';
foreach ($colors as $color) {
	$partial .= '<div class="badge badge-' . $color . '">' . $color . '</div>';
}
$html .= '<p><h4>.badge</h4>' . $partial . '</p>';

// ---
$partial = '';
foreach ($colors as $color) {
	$partial .= '<div class="badge badge-regular badge-' . $color . '">' . $color . '</div>';
}
$html .= '<p><h4>.badge .badge-regular</h4>' . $partial . '</p>';

// ---
$partial = '';
foreach ($colors as $color) {
	$partial .= '<div class="badge badge-' . $color . ' badge-large">' . $color . '</div>';
}
$html .= '<p><h4>.badge .badge-large</h4>' . $partial . '</p>';

// ---
$partial = '';
foreach ($colors as $color) {
	$partial .= '<div class="badge badge-' . $color . ' badge-small">' . $color . '</div>';
}
$html .= '<p><h4>.badge .badge-small</h4>' . $partial . '</p>';

// template
$tpl = Template::get();
$tpl->options([
	'thirdbar' => 'system.thirdbar'
]);
$tpl->title('Badge');
$tpl->content = '<div class="panel"><div class="panel-body">' . $html . '</div></div>';

return $tpl->response();
