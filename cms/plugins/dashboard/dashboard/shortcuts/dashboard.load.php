<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

return function (&$dashboard) {
    $config            = config('dashboard-shortcuts');
    $allow_cache    = $config['dashboard-shortcuts.shortcuts_allow_cache'];
    $menu_key        = $config['dashboard-shortcuts.shortcuts_key'];
    $html            = '';

    if ($allow_cache == -1) {
        $allow_cache = SYSTEM_ALLOW_CACHE;
    }
    if ($allow_cache) {
        $cache_key    = $menu_key . '#';
        $cache        = cache();
        $html        = $cache->get($cache_key);
    }

    // cache
    if (!$html) {
        $tiles = Tiles::get();
        $tiles->fromMenuKey($menu_key);
        $tiles->setOptions(['size' => 'small']);
        $tiles->separate('');
        $html = $tiles->render();

        $allow_cache and $cache->set($cache_key, $html);
    }

    $dashboard->row($html);
};
