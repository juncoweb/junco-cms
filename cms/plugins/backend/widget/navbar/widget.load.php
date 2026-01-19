<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Template\WidgetInterface;

return function (WidgetInterface $widget) {
    // vars
    $allow_cache = SYSTEM_ALLOW_CACHE;
    $menu_key    = 'backend-Default';
    $html        = '';

    if ($allow_cache) {
        $cache_key = $menu_key . '#';
        $cache     = cache();
        $html      = $cache->get($cache_key);
    }

    if (!$html) {
        $html = Menus::get('.complete', $menu_key)
            ->setWithEdges()
            ->render();

        $allow_cache and $cache->set($cache_key, $html);
    }

    $html = '<nav id="sidebar" class="navbar navbar-control" data-hash="' . router()->getHash() . '" aria-label="' . _t('Main menu') . '">'
        . $html
        . '</nav>';

    $widget->section([
        'content' => $html
    ]);
};
