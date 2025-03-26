<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

return function (&$widget) {
    $config = config('contact-widget');
    $widget->section([
        'title' => $config['contact-widget.map_show_title'] ? _t('How to get there') : '',
        'body' => $config['contact-widget.map_code'],
        'css' => 'widget-contact-map'
    ]);
};
