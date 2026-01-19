<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Template\WidgetInterface;

return function (WidgetInterface $widget) {
    // vars
    $allow_cache = SYSTEM_ALLOW_CACHE;
    $menu_key    = 'frontend-Main';
    $html        = '';

    if ($allow_cache) {
        $cache_key = $menu_key . '#';
        $cache     = cache();
        $html      = $cache->get($cache_key);
    }

    if (!$html) {
        $html = Menus::get('', $menu_key)->render();

        $allow_cache and $cache->set($cache_key, $html);
    }

    $widget->section([
        'content' => '<nav class="navbar">' . $html . '</nav>',
    ]);
};
