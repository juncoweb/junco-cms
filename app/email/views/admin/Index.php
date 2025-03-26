<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// vars
$tiles = Tiles::get();
$tiles->line([
    ['href' => url('admin/email/write'), 'icon' => 'fa-solid fa-paper-plane', 'caption' => _t('Write')],
]);
$tiles->separate(_t('Main'));
//
$tiles->line([
    ['href' => url('admin/email/message'), 'icon' => 'fa-solid fa-envelope-open-text', 'caption' => _t('Message')],
    ['href' => url('admin/email/debug'), 'icon' => 'fa-solid fa-bug', 'caption' => _t('Debug')],
]);
$tiles->separate(_t('Tools'));
//
$tiles->line([
    ['href' => url('admin/settings', ['key' => 'email']), 'icon' => 'fa-solid fa-gear', 'caption' => _t('Settings')],
]);
$tiles->separate(_t('Shortcuts'));

// template
$tpl = Template::get();
$tpl->title(_t('Email'), 'fa-solid fa-envelope');
$tpl->content = $tiles->render();

return $tpl->response();
