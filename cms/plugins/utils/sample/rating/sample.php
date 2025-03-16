<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

defined('IS_TEST') or die;

$box_tag = '<div class="mb-8"><h5>%s</h5>%s</div>';
$colors = ['default', 'primary', 'secondary', 'success', 'info', 'warning', 'danger'];
$sizes = ['small', 'medium', 'large'];
$html = '';

// Colors
$partial = '';
foreach ($colors as $color) {
	$partial .= '<div>' . $color . ' ' . snippet('rating', 'utils')->render(3, ['color' => $color]) . '</div>';
}
$html .= sprintf($box_tag, 'Colors', $partial);


// Sizes
$partial = '';
foreach ($sizes as $size) {
	$partial .= '<div>' . $size . ' ' . snippet('rating', 'utils')->render(3, ['size' => $size]) . '</div>';
}
$html .= sprintf($box_tag, 'Sizes', $partial);

// Form
$form = Form::get();
$form->load('utils.rating', ['name' => 'rating'])/* ->setLabel('Rating') */;
$html .= sprintf($box_tag, 'Form', $form->render());


// template
$tpl = Template::get();
$tpl->options([
	'css' => 'cms/scripts/utils/css/rating.css',
	'js' => 'cms/scripts/utils/js/rating.js',
	'domready' => 'JsRating({onSelect: function() { console.log(this.value); return true; }})'
]);
$tpl->title('Rating', 'fa-solid fa-star');
$tpl->content = '<div class="panel"><div class="panel-body">' . $html . '</div></div>';

return $tpl->response();
