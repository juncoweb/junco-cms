<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// form
$form = Form::get();
if ($values) {
    $form->setValues($values);
    $form->hidden('user_id');
}
$form->input('user_name', ['required' => true])->setLabel(_t('Name'))->setRequired();
$form->input('user_username', ['required' => true])->setLabel(_t('Username'))->setRequired();
$form->input('user_password', ['type' => 'password', 'placeholder' => _t('Change')])->setLabel(_t('Password'));
$form->input('user_email', ['type' => 'email', 'required' => true])->setLabel(_t('Email'))->setRequired();
$form->collection('roles', 'role_id')->setLabel(_t('Rol'));

// modal
$modal = Modal::get();
$modal->close();
$modal->enter();
$modal->title([_t('Users'), $title]);
$modal->content($form->render());

return $modal->response();
