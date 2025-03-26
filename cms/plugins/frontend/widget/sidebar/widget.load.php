<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

return function (&$widget) {
    $allow_cache    = SYSTEM_ALLOW_CACHE;
    $key            = config('frontend-sidebar.key');
    $html             = '';
    $rows            = null;
    $uri            = $_SERVER['REQUEST_URI'];

    if ($allow_cache) {
        $cache = cache();
        $rows = $cache->get($key);
    }

    if (!$rows) {
        $menus = new Menus($key);
        $rows  = [];

        foreach ($menus->read() as $row) {
            $params            = $row['menu_params'] ? json_decode($row['menu_params'], true) : false;
            $row['color']    = $params['color'] ?? false;
            $row['uri']        = $params['uri'] ?? false;
            $rows[]            = $row;
        }

        $allow_cache and $cache->set($key, $rows);
    }

    foreach ($rows as $row) {
        $html .= '<li' . ($uri == $row['uri'] ? ' class="selected"' : '') . '>'
            . '<a href="' . $row['menu_url'] . '"' . ($row['color'] ? ' style="background: ' . $row['color'] . ';"' : '') . '><i class="fa-solid fa-caret-right"></i>' . $row['menu_name'] . '</a>'
            . '</li>';
    }

    if ($html) {
        $widget->section([
            'content' => '<ul class="widget-list widget-sidebar">' . $html . '</ul>',
        ]);
    }
};
