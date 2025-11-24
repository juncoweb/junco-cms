<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// security
if (!empty($error)) {
    $html = '<p class="dialog dialog-warning">' . _t('This option is not available.') . '</p>';
} else {
    $email_opt = ['type' => 'email'];

    // form
    $form = Form::get();
    if ($user) {
        $form->setValues($user);
        $form->hidden('token', $token);
        $email_opt['readonly'] = 'readonly';
    }

    $form->input('fullname')->setLabel(_t('Name'));
    $form->input('username')->setLabel(_t('Username'));
    $form->input('password', ['type' => 'password'])->setLabel(_t('Password'));
    $form->input('verified', ['type' => 'password'])->setLabel(_t('Password Confirmation'));
    $form->input('email', $email_opt)->setLabel(_t('Email'));

    if ($legal_url) {
        $form->checkbox('legal')->setLabel(sprintf(
            _t('I have read and accepted the %sterms and conditions%s'),
            '<a href="' . $legal_url . '" target="_blank">',
            '</a>'
        ));
    }

    $form->enter(_t('Sign up'));
    $html = $form->render();

    // plugins
    foreach ($widgets as $row) {
        $html .= '<div class="usys-connect">'
            .  '<a href="' . htmlspecialchars($row['url']) . '" style="background: ' . $row['bg-color'] . ';">'
            .    '<i class="' . $row['icon'] . '"></i>'
            .    '<span>' . $row['caption'] . '</span>'
            . '</a></div>';
    }

    $html = '<p>' . _t('This is easy! Just complete the form.') . '</p>'
        . '<div class="panel mb-4 usys-wrapper usys-signup"><div class="panel-body">' . $html . '</div></div>';
}

// template
$tpl = Template::get();
$tpl->options($options);
$tpl->domready('Usys.signup()');
$tpl->title(_t('Sign up'));
$tpl->content = $html;

return $tpl->response();
