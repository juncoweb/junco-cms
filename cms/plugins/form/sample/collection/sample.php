<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

defined('IS_TEST') or die;

$curuser = curuser();

// form
$form = Form::get();
$form->setValues([
	'user_id'	=> $curuser->id,
	'__user_id' => $curuser->fullname,
	'label_id'	=> [0 => 'Hola', 1 => 'Chau'],
]);
$form->collection('userpicker', 'user_id')->setLabel('Select');
$form->collection('userpicker', 'user_idx')->setLabel('Select');
$form->collection('userpicker', 'label_id')->setLabel('Multiple');

// box
$fbox = Form::getBox();
$fbox->tab(_t('Main'), $form->render());
$html = $fbox->render();


// template
$tpl = Template::get();
$tpl->options([
	'js' => 'cms/plugins/form/sample/collection/scripts.js',
	'domready' => 'AdTest.load()',
	'thirdbar' => 'form.thirdbar'
]);
$tpl->title(_t('Collection'));
$tpl->content = '<div class="panel"><div class="panel-body">' . $html . '</div></div>';

return $tpl->response();
