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
			$color = ' btn-' . $color;
		} else {
			$caption = 'None';
		}
		$partial .= ' ' . strtr($element, ['{{ class }}' => $color, '{{ caption }}' => $caption]);
	}

	return '<div><h4>' . $title . '</h4>' . $partial . '</div>';
}

// vars
$html = '<h2>Regular</h2>';
$html .= render('btn', '<button class="btn{{ class }}">{{ caption }}</button>');
$html .= render('btn disabled', '<button class="btn{{ class }} disabled">{{ caption }}</button>');
$html .= render('btn btn-outline', '<button class="btn{{ class }} btn-outline">{{ caption }}</button>');
$html .= '<h2>Solid</h2>';
$html .= render('btn btn-solid', '<button class="btn btn-solid{{ class }}">{{ caption }}</button>');
$html .= render('btn btn-solid disabled', '<button class="btn btn-solid{{ class }} disabled">{{ caption }}</button>');
$html .= render('btn btn-solid btn-outline', '<button class="btn btn-solid{{ class }} btn-outline">{{ caption }}</button>');

// template
$tpl = Template::get();
$tpl->options([
	'css' => 'cms/plugins/form/sample/demo/sample.css',
	'thirdbar' => 'form.thirdbar'
]);
$tpl->title('Button');
$tpl->content = '<div class="panel"><div class="panel-body">' . $html . '</div></div>';

return $tpl->response();
