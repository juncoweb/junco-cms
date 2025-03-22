<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

defined('IS_TEST') or die;

// form
$form = Form::get();

// actions
$fac = $form->getActions();
$fac->enter();

// elements
$form->setValues([
	'date_2' => '2017-01-16T00:00:00',
	'date_3' => '2017-01-16'
]);

$form->input('date_1', ['control-felem' => 'datetime-local'])->setLabel('Datetime (custom)');
$form->input('date_2', ['type' => 'datetime-local'])->setLabel('Datetime (System)');
$form->input('date_3', ['type' => 'date'])->setLabel('Date (System)');
$form->addRow(['label' => 'Inline', 'content' => '<div id="date-test"></div>']);

$html = $form->render();
$domready = "
	JsFelem.load('#content');
	FeDate(null, {
		'inject': document.querySelector('#date-test'),
		'setDrop': false,
		'onSelect': function (date) {
			alert(date);
		}
	});";

// template
$tpl = Template::get();
$tpl->options([
	'js' => 'cms/plugins/form/sample/date/scripts.js,cms/scripts/form/js/date.js',
	'domready' => $domready,
	'thirdbar' => 'form.thirdbar'
]);
$tpl->title('Date');
$tpl->content = '<div class="panel"><div class="panel-body">' . $html . '</div></div>';

return $tpl->response();
