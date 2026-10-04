<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Template\WidgetInterface;

return function (?WidgetInterface $widget = null) {
    $rows = [
        ['href' => url('admin/assets.colors'), 'caption' => _t('Colors'), 'icon' => 'fa-solid fa-paintbrush'],
        ['href' => url('admin/assets.colors/contrast'), 'caption' => _t('Contrast'), 'icon' => 'fa-solid fa-circle-half-stroke'],
        ['href' => url('admin/assets.colors/palette'), 'caption' => _t('Palette'), 'icon' => 'fa-solid fa-palette'],
        ['href' => url('admin/assets.colors/checker'), 'caption' => _t('Checker'), 'icon' => 'fa-solid fa-square-check'],
    ];

    if (!$widget) {
        array_shift($rows);

        return $rows;
    }

    $html = '';
    foreach ($rows as $row) {
        $html .= '<li>'
            . '<a href="' . $row['href'] . '">'
            . '<i class="' . $row['icon'] . '"></i> '
            . '<span>' . $row['caption'] . '</span>'
            . '</a></li>';
    }

    $widget->section([
        'content' => '<div class="widget-thirdbar" control-tpl="thirdbar">'
            . '<ul>' . $html . '</ul>'
            . '</div>'
    ]);
};
