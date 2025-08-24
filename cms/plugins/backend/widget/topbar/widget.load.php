<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

return function (&$widget) {
    $user_id = curuser()->getId();
    $html = '<a href="' . url('/') . '" class="th-btn" title="' . ($t = _t('View site')) . '" aria-label="' . $t . '"><i class="fa-solid fa-globe"></i></a>';

    // theme
    $html .= '<div class="btn-group">'
        . '<button type="button" control-felem="dropdown" control-tpl="theme" role="caret" class="th-btn"><span data-select-label><i class="fa-solid fa-sun" aria-hidden="true"></i></span></button>'
        . '<div role="drop-menu" class="dropdown-menu" style="display: none;">'
        .  '<ul>'
        .   '<li><a href="javascript:void(0)" data-value="light"><i class="fa-solid fa-sun"></i> <span>' . _t('Light') . '</span></a></li>'
        .   '<li><a href="javascript:void(0)" data-value="dark"><i class="fa-solid fa-moon"></i> <span>' . _t('Dark') . '</span></a></li>'
        .   '<li><a href="javascript:void(0)" data-value="auto"><i class="fa-solid fa-circle-half-stroke"></i> <span>' . _t('Auto') . '</span></a></li>'
        .  '</ul>'
        . '</div>'
        . '</div>';


    if ($user_id) {
        $total = db()->query("
		SELECT COUNT(*)
		FROM `#__notifications`
		WHERE user_id = ?
		AND read_at IS NULL", $user_id)->fetchColumn();

        $html .= '<a href="javascript:void(0)" class="th-btn" control-tpl="notifications" title="' . ($t = _t('Notifications')) . '" aria-label="' . $t . '">'
            . '<i class="fa-solid fa-bell" aria-hidden="true"></i>'
            . ($total ? '<span class="badge badge-danger badge-small">' . $total  . '</span>' : '')
            . '</a>';

        $html .= '<a href="javascript:void(0)" class="th-btn" control-tpl="logout" title="' . ($t = _t('Log out')) . '" aria-label="' . $t . '"><i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i></a>';
    }

    $html .= '<a href="javascript:void(0)" class="th-btn pull-btn" title="' . ($t = _t('Open main menu')) . '" role="button" aria-label="' . $t . '"><i class="fa-solid fa-bars"></i></a>';


    $widget->section([
        'content' => $html,
        'css' => 'layout-topbar'
    ]);
};
