<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Template\WidgetInterface;

return function (WidgetInterface $widget) {
    $rows        = null;
    $allow_cache = SYSTEM_ALLOW_CACHE;
    $menu_key    = 'my-Default';

    if ($allow_cache) {
        $cache_key = $menu_key . '#';
        $cache     = cache();
        $rows      = $cache->get($cache_key);
    }

    if (!$rows) {
        $menus = new Menus($menu_key);
        $rows  = [];

        foreach ($menus->read() as $row) {
            $params = json_decode($row['menu_params'], true);

            if (isset($params['label_id'])) {
                if (!is_array($params['label_id'])) {
                    $params['label_id'] = [constant($params['label_id'])];
                } else {
                    foreach ($params['label_id'] as $i => $label_id) {
                        $params['label_id'][$i] = constant($label_id);
                    }
                }
            } else {
                $params['label_id'] = null;
            }

            $rows[] = [
                'menu_name'  => $row['menu_name'],
                'menu_url'   => $row['menu_url'],
                'menu_image' => $row['menu_image'],
                'menu_hash'  => $row['menu_hash'],
                'label_id'   => $params['label_id'],
            ];
        }
        $allow_cache and $cache->set($cache_key, $rows);
    }

    $hash        = app('assets')->getOption('hash');
    $permissions = curuser()->getPermissions();
    $html        = '';

    foreach ($rows as $row) {
        if (!$row['label_id'] || array_intersect($row['label_id'], $permissions)) {
            $html .= '<li' . ($row['menu_hash'] == $hash ? ' class="selected"' : false) . '>'
                . '<a href="' . $row['menu_url'] . '" title="' . $row['menu_name'] . '">'
                .   '<i class="' . $row['menu_image'] . ' widget-icon"></i>'
                .    $row['menu_name'] //.'<div class="badge badge-primary badge-small float-right">12</div>'
                . '</a>'
                . '</li>';
        }
    }

    $widget->section([
        'content' => '<ul class="widget-list">' . $html . '</ul>',
        'css' => 'my-widget'
    ]);
};
