<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// form
$form = Form::get();
$form->input('email_username')->setLabel(_t('Email/Username'));
$form->enter();

// template
$tpl = Template::get();
$tpl->options($options);
$tpl->domready('UsysPassword.reset()');
$tpl->title(_t('Reset Password'));
$tpl->content = '<div class="panel mb-4 usys-wrapper usys-reset-pwd"><div class="panel-body">' . $form->render() . '</div></div>'
    . '<p class="dialog dialog-warning">' . _t('By completing the form will be sent to your email an application for a new password.') . '</p>'
    . '<p class="dialog dialog-warning">' . _t('If for some reason your email must be changed, this form will not be useful, and the new password must be requested via administration.') . '</p>';

return $tpl->response();
