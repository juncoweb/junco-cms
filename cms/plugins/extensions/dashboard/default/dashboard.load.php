<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Dashboard\DashboardInterface;

return function (DashboardInterface $dashboard) {
    $cache = cache();
    $html = $cache->get('extensions-updates#');

    if (!$html) {
        app('assets')->js('assets/extensions-dashboard.min.js');
        $html = '<div id="extensions-updates">'
            . '<form>'
            .  '<input type="hidden" name="option" value="1">'
            .  FormSecurity::getToken()
            . '</form>'
            . '</div>';
    }

    if ($html != 'null') {
        $dashboard->section($html);
    }
};
