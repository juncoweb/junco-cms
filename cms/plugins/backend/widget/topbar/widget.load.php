<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Template\WidgetInterface;

return function (WidgetInterface $widget) {
    $html = '';

    // notifications
    $html .= '<button type="button" class="th-btn" control-tpl="notifications" title="' . _t('Notifications') . '">'
        . '<i class="fa-solid fa-bell" aria-hidden="true"></i>'
        . '<span class="badge badge-danger badge-small rounded-full" style="display: none;"></span>'
        . '</button>';

    // dropdown
    $colors = '';
    foreach (['default', 'primary', 'secondary', 'info', 'success', 'warning', 'danger'] as $color) {
        $colors .= '<a href="javascript:void(0)" role="button" data-value="' . $color . '">'
            .  '<i class="fa-solid fa-circle color-' . $color . '" aria-hidden="true"></i>'
            .  '<div class="visually-hidden">' . $color . '</div>'
            . '</a>';
    }

    $options = '';
    $options .= '<li><a href="' . url('/') . '"><i class="fa-solid fa-globe" aria-hidden="true"></i> ' . _t('View site') . '</a></li>';
    $options .= '<li><a href="javascript:void(0)" role="button" control-tpl="logout"><i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i> ' . _t('Log out') . '</a></li>';
    $options .= '<li class="separator" role="separator"></li>';
    $options .= '<li><div class="theme-selector" control-tpl="theme">'
        .   '<a href="javascript:void(0)" role="button" data-value="light" title="' . _t('Light') . '" class="active-on-light"><i class="fa-solid fa-sun" aria-hidden="true"></i></a>'
        .   '<a href="javascript:void(0)" role="button" data-value="auto" title="' . _t('Auto') . '" class="active-on-auto"><i class="fa-solid fa-circle-half-stroke" aria-hidden="true"></i></a>'
        .   '<a href="javascript:void(0)" role="button" data-value="dark" title="' . _t('Dark') . '" class="active-on-dark"><i class="fa-solid fa-moon" aria-hidden="true"></i></a>'
        . '</div></li>';
    $options .= '<li class="separator" role="separator"></li>';
    $options .= '<li><div class="theme-color" control-tpl="color" role="group" aria-label="' . _t('Theme color') . '">' . $colors . '</div></li>';

    $html .= '<div class="btn-group">'
        . '<button type="button" control-felem="dropdown" title="' . _t('Top menu') . '" class="th-btn">'
        .   '<i class="fa-solid fa-ellipsis" aria-hidden="true"></i>'
        .  '</button>'
        . '<div class="dropdown-menu" style="display: none;">'
        .  '<ul>' . $options . '</ul>'
        . '</div>'
        . '</div>';

    $html .= '<button type="button" class="th-btn pull-btn" title="' . _t('Open main menu') . '">'
        .  '<i class="fa-solid fa-bars" aria-hidden="true"></i>'
        . '</button>';


    $widget->section([
        'content' => $html,
        'css' => 'layout-topbar'
    ]);
};
