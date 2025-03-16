<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// form
$form = Form::get();
$form->setValues($values);
$form->hidden('id');
//
$form->input('fullname')->setLabel(_t('Name'));
$form->input('username')->setLabel(_t('Username'));
$form->input('__password', ['type' => 'password'])->setLabel(_t('Password'))->setRequired();
$form->input('password', ['type' => 'password'])->setLabel(_t('New password'));
$form->input('email')->setLabel(_t('Email'));
$form->enter();

// template
$tpl = Template::get();
$tpl->options($options);
$tpl->domready('UsysAccount()');
$tpl->title(_t('Account'));
$tpl->content = '<div class="panel mb-4 usys-wrapper usys-account"><div class="panel-body">' . $form->render() . '</div></div>';

return $tpl->response();
