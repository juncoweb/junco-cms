<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Dashboard\DashboardInterface;

return function (DashboardInterface $dashboard) {
    $allow_cache = SYSTEM_ALLOW_CACHE;
    $html        = '';

    if ($allow_cache) {
        $cache_key = 'contact-ha#';
        $cache     = cache();
        $html      = $cache->get($cache_key);
    }

    // cache
    if (!$html) {
        $reports = new ContactReport();
        $chart = '<div class="panel">'
            .   '<div class="panel-header"><h5>' . _t('Contact') . '</h5></div>'
            .   '<div class="panel-body">'
            .      '<div data-chart="line" style="display: none;">' . json_encode($reports->getChartData()) . '</div>'
            .   '</div>'
            . '</div>';

        $data = $reports->getData();
        $details = '<h4>'
            . _t('Contact')
            . ' (<span>' . $data['num_messages'] . '</span>)'
            . '<a href="' . url('admin/contact') . '"><i class="fa-solid fa-external-link float-right"></i></a>'
            . '</h4>';

        if ($data['created_at']) {
            $details .= '<span class="color-subtle-default">' . sprintf(_t('Last %s'), $data['created_at']->format(_t('Y-M-d'))) . '</span>';
        }

        //
        $html = '<div class="grid grid-21 grid-responsive mb-4">'
            . '<div>' . $chart . '</div>'
            . '<div><div class="panel"><div class="panel-body">' . $details . '</div></div></div>'
            . '</div>';

        $allow_cache and $cache->set($cache_key, $html);
    }

    $dashboard->section($html);
};
