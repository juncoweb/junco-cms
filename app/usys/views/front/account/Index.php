<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

// form
$form = Form::get();
$form->setValues($values);
$form->hidden('id');
//
$form->input('user_name')->setLabel(_t('Name'));
$form->input('user_username')->setLabel(_t('Username'));
$form->input('__user_password', ['type' => 'password'])->setLabel(_t('Password'))->setRequired();
$form->input('user_password', ['type' => 'password'])->setLabel(_t('New password'));
$form->input('user_email')->setLabel(_t('Email'));
$form->enter();

$html = '<div class="panel mb-4 usys-wrapper usys-account"><div class="panel-body">' . $form->render() . '</div></div>';

// template
$tpl = Template::get();
$tpl->options($options);
$tpl->domready('UsysAccount()');
$tpl->title(_t('Account'));
$tpl->content($html);

return $tpl->response();
