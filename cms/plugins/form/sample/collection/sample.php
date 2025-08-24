<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

defined('IS_TEST') or die;

$curuser = curuser();
$domready = "
	/*JsFelem.implement({
		'test': function(el) {
			JsCollection(el, {
				url: JsUrl('admin/usys.users/json'),
			});
		},
	});
	*/
	JsCollection(document.querySelectorAll('[control-felem=test]'), {
		justUse: 1,
		url: JsUrl('admin/usys.users/json'),
	});
	JsCollection(document.querySelectorAll('[control-felem=userpicker]'), {
		justUse: 1,
		url: JsUrl('admin/usys.users/json'),
	});
	JsCollection(document.querySelectorAll('[control-felem=xuserpicker]'), {
		justUse: 1,
		url: JsUrl('admin/usys.users/json'),
	});
	//JsFelem.load(document);
	//JsForm().request();
";
// form
$form = Form::get();
$form->setValues([
    'user_id'    => $curuser->getId(),
    '__user_id' => $curuser->getName(),
    'label_id'    => [0 => 'Hola', 1 => 'Chau'],
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
    'domready' => $domready,
    'thirdbar' => 'form.thirdbar'
]);
$tpl->title(_t('Collection'));
$tpl->content = '<div class="panel"><div class="panel-body">' . $html . '</div></div>';

return $tpl->response();
