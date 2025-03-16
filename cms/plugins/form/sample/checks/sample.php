<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

defined('IS_TEST') or die;


function render($title, $element)
{
	$colors = ['default', 'primary', 'secondary', 'success', 'info', 'warning', 'danger'];
	$partial = '';

	foreach ($colors as $color) {
		if ($color) {
			$caption = ucfirst($color);
			$color = ' input-' . $color;
		} else {
			$caption = 'None';
		}
		$partial .= '<div style="display: block; max-widht: 400px; margin: 3px;">'
			. strtr($element, ['{{ class }}' => $color, '{{ caption }}' => $caption])
			. '</div>';
	}

	return '<div><h4>' . $title . '</h4>' . $partial . '</div>';
}

// vars
$html = '';
$html .= render('.input-checkbox', '<label><input class="input-checkbox{{ class }}" type="checkbox"/> {{ caption }}</label>');
$html .= render('.input-checkbox', '<label><input class="input-toggle{{ class }}" type="checkbox"/> {{ caption }}</label>');
$html .= render('.input-radio', '<label><input name="radio" type="radio" class="input-radio{{ class }}"/> {{ caption }}</label>');

// template
$tpl = Template::get();
$tpl->options(['thirdbar' => 'form.thirdbar']);
$tpl->title('Checks');
$tpl->content = '<div class="panel"><div class="panel-body">' . $html . '</div></div>';

return $tpl->response();
