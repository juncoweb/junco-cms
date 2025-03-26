<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// form
$form = Form::get();

if ($user) {
    $form->addRow(['content' => $user['fullname']]);
    $form->setValues($user);
}

$form->input('email_username', ['placeholder' => _t('Email/Username'), 'icon' => 'fa-solid fa-user']);
$form->input('password', ['type' => 'password', 'placeholder' => _t('Password'), 'icon' => 'fa-solid fa-key']);

$element = '';
if ($not_expire) {
    $form->checkbox('not_expire')->setLabel(_t('Stay logged in'));
    $element = $form->getLastElement();
}

$form->addRow(['content' => $element . '<a href="' . url('/usys.password/reset') . '" class="reset-pwd">' . _t('I forgot my password') . '</a>']);
$form->addRow(['content' => sprintf(_t('Don\'t have any account? Sign up %shere%s'), '<a href="' . url('/usys/signup') . '">', '</a>')]);
$form->enter(_t('Log in'));

if ($redirect) {
    $form->hidden('redirect', $redirect);
}

$html = $form->render();

// widgets
foreach ($widgets as $widget) {
    $html .= '<div class="usys-connect">'
        .  '<a href="' . htmlspecialchars($widget['url']) . '" style="background: ' . $widget['bg-color'] . ';">'
        .    '<i class="' . $widget['icon'] . '"></i>'
        .    '<span>' . $widget['caption'] . '</span>'
        . '</a></div>';
}

if (router()->isFormat('modal')) {
    // modal
    $modal = Modal::get();
    $modal->title(_t('Log in'));
    $modal->content = '<div class="usys-modal">' . $html . '</div>';
    return $modal->response();
} else {
    // template
    $tpl = Template::get();
    $tpl->options($options);
    $tpl->domready('Usys.load()');
    $tpl->title(_t('Log in'));
    $tpl->content = '<div class="panel mb-4 usys-wrapper usys-login"><div class="panel-body">' . $html . '</div></div>';
    return $tpl->response();
}
