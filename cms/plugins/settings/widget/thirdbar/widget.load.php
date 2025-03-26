<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

return function (&$widget) {
    // vars
    $allow_cache    = SYSTEM_ALLOW_CACHE;
    $menu_key        = 'settings-Default';
    $html            = '';

    if ($allow_cache) {
        $cache_key    = $menu_key    . '#';
        $cache        = cache();
        $html        = $cache->get($cache_key);
    }

    if (!$html) {
        $menus = Menus::get('.complete', $menu_key);
        $menus->setWithEdges();
        $html = $menus->render();

        $allow_cache and $cache->set($cache_key, $html);
    }

    $widget->section([
        'content' => '<div class="widget-thirdbar" control-tpl="thirdbar">' . $html . '</div>'
    ]);
};
