<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// form
$form = Form::get();
if ($values) {
	$form->setValues($values);
	$form->hidden('language');
}
//
$form->input('name')->setLabel(_t('Name'))->setRequired();

// modal
$modal = Modal::get();
$modal->title($title);
$modal->enter();
$modal->close();
$modal->content = $form->render();

return $modal->response();
