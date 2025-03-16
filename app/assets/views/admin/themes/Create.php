<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// form
$form = Form::get();
$form->setValues($values);
//
$form->select('extension_alias', $extensions)->setLabel(_t('Extension'))->setRequired();
$form->input('name')->setLabel(_t('Name'));
$form->hidden('from');

// modal
$modal = Modal::get();
$modal->title($title);
$modal->enter();
$modal->close();
$modal->content = $form->render();

return $modal->response();
