<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Template\WidgetInterface;

return function (WidgetInterface $widget) {
    $html = '';

    // notifications
    $html .= '<a href="javascript:void(0)" class="th-btn" control-tpl="notifications" title="' . ($t = _t('Notifications')) . '" aria-label="' . $t . '">'
        . '<i class="fa-solid fa-bell" aria-hidden="true"></i>'
        . '<span class="badge badge-danger badge-small rounded-full" style="display: none;"></span>'
        . '</a>';

    // dropdown
    $options = '';
    $options .= '<li><a href="' . url('/') . '" title="' . ($t = _t('View site')) . '" ><i class="fa-solid fa-globe" aria-hidden="true"></i> ' . $t . '</a></li>';
    $options .= '<li><a href="javascript:void(0)" control-tpl="logout" title="' . ($t = _t('Log out')) . '"><i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i> ' . $t . '</a></li>';
    $options .= '<li class="separator"></li>';
    $options .= '<li><div class="theme-selector" control-tpl="theme">'
        .   '<a href="javascript:void(0)" data-value="light" title="' . ($t = _t('Light')) . '" class="active-on-light"><i class="fa-solid fa-sun" aria-hidden="true"></i><div class="visually-hidden">' . $t . '</div></a>'
        .   '<a href="javascript:void(0)" data-value="auto" title="' . ($t = _t('Auto')) . '" class="active-on-auto"><i class="fa-solid fa-circle-half-stroke" aria-hidden="true"></i><div class="visually-hidden">' . $t . '</div></a>'
        .   '<a href="javascript:void(0)" data-value="dark" title="' . ($t = _t('Dark')) . '" class="active-on-dark"><i class="fa-solid fa-moon" aria-hidden="true"></i><div class="visually-hidden">' . $t . '</div></a>'
        . '</div></li>';

    $colors = '';
    foreach (['default', 'primary', 'secondary', 'info', 'success', 'warning', 'danger'] as $color) {
        $colors .= '<a href="javascript:void(0)" data-value="' . $color . '">'
            .  '<i class="fa-solid fa-circle color-' . $color . '" aria-hidden="true"></i>'
            .  '<div class="visually-hidden">' . $color . '</div>'
            . '</a>';
    }

    $options .= '<li class="separator"></li>';
    $options .= '<li><div class="theme-color" control-tpl="color">' . $colors . '</div></li>';

    $html .= '<div class="btn-group">'
        . '<button type="button" control-felem="dropdown" role="caret" class="th-btn"><i class="fa-solid fa-ellipsis" aria-hidden="true"></i></button>'
        . '<div role="drop-menu" class="dropdown-menu" style="display: none;">'
        .  '<ul>' . $options . '</ul>'
        . '</div>'
        . '</div>';

    $html .= '<a href="javascript:void(0)" class="th-btn pull-btn" title="' . ($t = _t('Open main menu')) . '" role="button" aria-label="' . $t . '"><i class="fa-solid fa-bars"></i></a>';


    $widget->section([
        'content' => $html,
        'css' => 'layout-topbar'
    ]);
};
