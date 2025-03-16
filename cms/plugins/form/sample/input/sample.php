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
		$caption = ucfirst($color);
		$partial .= '<div style="display: inline-block; max-widht: 400px; margin: 3px;">'
			. strtr($element, ['{{ color }}' => $color, '{{ caption }}' => $caption])
			. '</div>';
	}

	return '<div><h4>' . $title . '</h4>' . $partial . '</div>';
}

// vars
$html = '';
$html .= render('.input', '<input class="input-field input-{{ color }}" placeholder="{{ caption }}" />');
$html .= render('.input-group', '<div class="input-icon-group input-{{ color }}"><input class="input-field" placeholder="{{ caption }}"><div class="input-icon"><i class="fa-solid fa-search"></i></div></div>');
$html .= render('.input-group', '<div class="input-group input-{{ color }}"><span class="btn"><i class="fa-solid fa-user"></i></span><input type="text" class="input-field" placeholder="{{ caption }}"><input type="text" class="input-field" placeholder="Input 2"></div>');
$html .= render('textarea.input-field', '<textarea class="input-field input-{{ color }}" placeholder="{{ caption }}" control-felem="auto-grow"></textarea>');
$html .= render('.input', '<div class="panel panel-solid panel-{{ color }}" style="padding: 10px"><div class="input-icon-group input-solid input-{{ color }}"><input class="input-field" placeholder="Solid {{ caption }}"><div class="input-icon"><i class="fa-solid fa-search"></i></div></div></div>');

// template
$tpl = Template::get();
$tpl->options([
	'domready' => "JsFelem.load('#content')",
	'thirdbar' => 'form.thirdbar'
]);
$tpl->title('Input');
$tpl->content = '<div class="panel"><div class="panel-body">' . $html . '</div></div>';

return $tpl->response();
