<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

defined('IS_TEST') or die;

$domready = "
var _Form = JsForm().request(JsUrl('admin/sa/request'), function(message) {
	_Form.notify(message)
});
";
// form
$form = Form::get();

// actions
$fac = $form->getActions();
$fac->enter();
//$fac->reset();

// elements
$form->input('name')->setLabel(_t('Name'));
$form->file('image', [
	'accept' => 'image',
	'max_size' => 12436466,
	//'images' => [media_url('slider/small'), '1-20170107015049.jpg', 'none.gif']
])->setLabel(_t('Image'));

$form->file('images', [
	'multiple' => true,
	//'accept' => 'image',
	'max_size' => 12436466,
	'images' => [media_url('slider/small'), ['1-20170107015057.jpg'], 'none.gif']
])->setLabel(_t('Images'));
$html = $form->render();

// template
$tpl = Template::get();
$tpl->options([
	'domready' => $domready,
	'thirdbar' => 'form.thirdbar'
]);
$tpl->title(_t('Upload file'));
$tpl->content = '<div class="panel"><div class="panel-body">' . $html . '</div></div>';

return $tpl->response();
