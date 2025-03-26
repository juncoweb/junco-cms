<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// form
$form = Form::get();
if ($values) {
    $form->setValues($values);
    $form->hidden('user_id');
}
$form->input('fullname', ['required' => true])->setLabel(_t('Name'))->setRequired();
$form->input('username', ['required' => true])->setLabel(_t('Username'))->setRequired();
$form->input('password', ['type' => 'password', 'placeholder' => _t('Change')])->setLabel(_t('Password'));
$form->input('email', ['type' => 'email', 'required' => true])->setLabel(_t('Email'))->setRequired();
$form->collection('roles', 'role_id')->setLabel(_t('Rol'));

// modal
$modal = Modal::get();
$modal->close();
$modal->enter();
$modal->title([_t('Users'), $title]);
$modal->content = $form->render();

return $modal->response();
