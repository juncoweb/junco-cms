<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
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
    'date_3' => '2017-01-16T00:00:00',
    'date_4' => '2017-01-16'
]);

$form->input('date_1', ['control-felem' => 'date'])->setLabel('Datetime (custom)');
$form->input('date_2', ['control-felem' => 'datetime-local'])->setLabel('Datetime (custom)');
$form->input('date_3', ['type' => 'datetime-local'])->setLabel('Datetime (System)');
$form->input('date_4', ['type' => 'date'])->setLabel('Date (System)');
$form->element('<div id="date-test"></div>')->setLabel('Inline');
$form->button(['label' => 'Show', 'id' => 'date_4_btn_1']);
$form->button(['label' => 'Hide', 'id' => 'date_4_btn_2']);

$html = $form->render();
$domready = "
	JsFelem.load('#content');
	var dp = JsDatepicker('#date-test', {
		dropdown: false,
        inline: true,
		onSelect: function (date) {
			alert(date);
		}
	});
    document.getElementById('date_4_btn_1').addEventListener('click', function() { dp.show() });
    document.getElementById('date_4_btn_2').addEventListener('click', function() { dp.hide() });";

// template
$tpl = Template::get();
$tpl->options([
    'domready' => $domready,
    'thirdbar' => 'form.thirdbar'
]);
$tpl->title('Date');
$tpl->content('<div class="panel"><div class="panel-body">' . $html . '</div></div>');

return $tpl->response();
