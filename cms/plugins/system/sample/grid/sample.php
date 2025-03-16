<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

defined('IS_TEST') or die;

function grid__get_panels(string $css, int $total)
{
	$html = '';
	for ($i = 0; $i < $total; $i++) {
		$html .= '<div class="panel panel-regular"><div class="panel-body text-center"><span class="panel-stat-value">' . ($i + 1) . '</span></div></div>';
	}

	return '<h3>.' . $css . '</h3>'
		. '<div class="' . $css . ' mb-4">' . $html . '</div>';
}

$html = '';
$html .= grid__get_panels('grid', 2);
$html .= grid__get_panels('grid grid-2', 3);
$html .= grid__get_panels('grid grid-3', 5);
$html .= grid__get_panels('grid grid-4', 7);

$html .= grid__get_panels('grid grid-2 grid-responsive', 3);
$html .= grid__get_panels('grid grid-4 grid-responsive', 7);

$html .= grid__get_panels('grid grid-12', 4);
$html .= grid__get_panels('grid grid-13', 4);
$html .= grid__get_panels('grid grid-21', 4);
$html .= grid__get_panels('grid grid-31', 4);

$html .= grid__get_panels('grid grid-21 grid-responsive', 4);
$html .= grid__get_panels('grid grid-31 grid-responsive', 4);

$html .= grid__get_panels('grid grid-small-box', 7);
$html .= grid__get_panels('grid grid-medium-box', 7);
$html .= grid__get_panels('grid grid-large-box', 7);

// template
$tpl = Template::get();
$tpl->options([
	'thirdbar' => 'system.thirdbar'
]);
$tpl->title('Table');
$tpl->content = '<div class="panel"><div class="panel-body">' . $html . '</div></div>';

return $tpl->response();
