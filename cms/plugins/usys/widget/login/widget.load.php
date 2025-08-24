<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

return function (&$widget) {
    $curuser = curuser();
    if ($curuser->getId()) {
        $title = $curuser->getName();
        $html = '<ul class="widget-list">'
            .     (config('system.statement') == 21
                ? '<li><a href="' . url('/profile') . '">' . _t('Profile') . '</a></li>'
                : '<li><a href="' . url('/usys.account') . '">' . _t('Account') . '</a></li>')
            .     '<li><a href="my/">' . _t('My space') . '</a></li>'
            .     ($curuser->isAdmin() ? '<li><a href="admin/">' . _t('Administration') . '</a></li>' : '')
            .     '<li><a href="javascript:void(0)" control-tpl="logout">' . _t('Log out') . '</a></li>'
            .   '</ul>';
    } else {
        $title = _t('Log in');
        $html = '<form id="widget-usys-form">'
            . '<p><div class="input-icon-group"><span class="input-icon"><i class="fa-solid fa-user"></i></span><input type="text" name="email_username" class="input-field" placeholder="' . _t('Username') . '"/></div></p>'
            . '<p><div class="input-icon-group"><span class="input-icon"><i class="fa-solid fa-key"></i></span><input type="text" name="password" class="input-field" placeholder="' . _t('Password') . '"/></div></p>'
            . '<p><label class="input-label"><input type="checkbox" name="remember" value="1"/> ' . _t('Stay logged in') . '</label></p>'
            . '<p><button type="submit" class="btn btn-small">' . _t('Log in') . '</button></p>'
            . FormSecurity::getToken()
            . '</form>';
    }

    $widget->section([
        'title' => $title,
        'content' => $html,
        'css' => 'widget-login'
    ]);
};
