<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// form
$form = Form::get();
$form->hidden('option', 1);

// 1
$form->header('<h2>' . sprintf(_t('Instance %d'), 1) . '</h2>');
$form->input('email_username', ['maxlength' => 48])->setLabel(_t('Email/Username'));
$form->enter();
$form->separate();

// 2
$form->header('<h2>' . sprintf(_t('Instance %d'), 2) . '</h2>');
$form->group(
    $form->input('cur_email', ['readonly' => ''])->setLabel(_t('Email')),
    $form->button(['title' => _t('Change'), 'icon' => 'fa-solid fa-pencil'])
);

$form->group(
    $form->input('new_email', ['type' => 'email'])->setLabel(_t('Email')),
    $form->button(['title' => _t('Cancel'), 'icon' => 'fa-solid fa-xmark'])
);

$form->enter();

// template
$tpl = Template::get();
$tpl->options($options);
$tpl->domready('UsysActivation.reset()');
$tpl->title(_t('Reset activation'));
$tpl->content = '<div class="panel mb-4 usys-wrapper usys-reset-act"><div class="panel-body">' . $form->render() . '</div></div>'
    . '<p class="dialog dialog-warning">' . _t('At the end of the order, the previous activation messages will be deleted') . '</p>';

return $tpl->response();
