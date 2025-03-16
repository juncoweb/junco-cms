<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// form
$form = Form::get();

// actions
$fac = $form->getActions();
$fac->enter();
$fac->cancel();

// elements
$form->setValues($values);
if ($is_edit) {
	$form->hidden('to_verify');
	$form->hidden('key');
}
$form->textarea('assets', ['auto-grow' => '', 'data-min-height' => 120]);
$html_1 = $form->getLastElement();
$form->textarea('default_assets', ['auto-grow' => '', 'data-min-height' => 120, 'readonly' => '']);
$html_2 = $form->getLastElement();

// tabs
$tabs = Tabs::get('', 'assets-tabs');
$tabs->tab(_t('Current'), $html_1);
$tabs->tab(_t('Default'), $html_2);

//
$form->select('extension_alias', $extensions)->setLabel(_t('Extension'))->setRequired();
$form->input('name')->setLabel(_t('Name'));
$form->radio('type', ['css' => 'Css', 'js' => 'Js'])->setLabel(_t('Type'));
$form->addRow(['content' => $tabs->render()]);

// modal
$modal = Modal::get();
$modal->title($title);
$modal->content = $form->render();

return $modal->response();
