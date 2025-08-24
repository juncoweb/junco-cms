<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// template
$tpl = Template::get();
$tpl->options($options);

if ($token) {
    // table
    $form = Form::get();
    $form->hidden('token', $token);
    //
    $form->input('password', ['type' => 'password'])->setLabel(_t('Password'));
    $form->input('verified', ['type' => 'password'])->setLabel(_t('Confirm'));
    $form->enter();

    //
    $tpl->domready('UsysPassword.edit()');
    $tpl->title(_t('Edit Password'));
    $tpl->content = '<div class="panel mb-4 usys-wrapper usys-edit-pwd"><div class="panel-body">' . $form->render() . '</div></div>'
        . '<p class="dialog dialog-warning">' . _t('Please enter your password twice to save.') . '</p>';
} else {
    $tpl->title(_t('Edit Password'), ['document_title' => _t('Error Page')]);
    $tpl->content = _t('The code used is invalid or has expired.');
}

return $tpl->response();
