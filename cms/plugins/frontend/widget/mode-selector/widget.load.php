<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Template\WidgetInterface;

return function (WidgetInterface $widget) {
    $allow_cache = SYSTEM_ALLOW_CACHE;
    $html        = '';

    if ($allow_cache) {
        $cache_key = 'widget-mode-selector#';
        $cache     = cache();
        $html      = $cache->get($cache_key);
    }

    if (!$html) {
        $html = '<div control-tpl="theme" class="btn-group btn-small rounded-full">'
            . '<button class="btn" data-value="light"><i class="fa-solid fa-sun" aria-hidden="true"></i><div class="visually-hidden">' . _t('Light') . '</div></botton>'
            . '<button class="btn" data-value="auto"><i class="fa-solid fa-circle-half-stroke" aria-hidden="true"></i><div class="visually-hidden">' . _t('Auto') . '</div></botton>'
            . '<button class="btn" data-value="dark"><i class="fa-solid fa-moon" aria-hidden="true"></i><div class="visually-hidden">' . _t('Dark') . '</div></botton>'
            . '</div>';

        $allow_cache and $cache->set($cache_key, $html);
    }

    $widget->section([
        'content' => '<ul class="widget-list widget-sidebar">' . $html . '</ul>',
    ]);
};
