<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

defined('IS_TEST') or die;

$html = '';
$rows = [
	['Tooltip extracted from title', system__tooltip('<a href="javascript:void(0)" data-tooltip="{{ position }}" title="Tooltip">Tooltip on {{ position }}</a>')],
	['Tooltip blocked', system__tooltip('<a href="javascript:void(0)" data-tooltip="blocked {{ position }}" title="Tooltip">Tooltip on {{ position }}</a>')],
	['Tooltip extracted from ID', system__tooltip('<a href="javascript:void(0)" data-tooltip="blocked {{ position }}" data-value="#tootip-{{ position }}">Tooltip on {{ position }}</a><div id="tootip-{{ position }}" class="tooltip"><h3>Hello!</h3>Lorem Ipsum</div>')],
];

foreach ($rows as $row) {
	$html .= '<div class="panel mb-4">'
		.   '<div class="panel-header"><h2>' . $row[0] . '</h2></div>'
		.   '<div class="panel-body">' . $row[1] . '</div>'
		. '</div>';
}

// template
$tpl = Template::get();
$tpl->options([
	'domready' => 'Tooltip()',
	'thirdbar' => 'system.thirdbar'
]);
$tpl->title('Tooltip');
$tpl->content = $html;

return $tpl->response();


function system__tooltip(string $tag): string
{
	$tooptip = [];
	$positions = ['top', 'bottom', 'left', 'right'];

	foreach ($positions as $position) {
		$tooptip[] = strtr($tag, ['{{ position }}' => $position]);
	}

	return implode(' | ', $tooptip);
}
