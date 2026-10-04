<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Template\WidgetInterface;

return function (WidgetInterface $widget) {
    $t1 = _t('Search');
    $t2 = $t1 . ' (Alt+z)';

    $html = '<div class="input-icon-group">'
        .  '<input'
        .  ' type="search"'
        .  ' name="search"'
        .  ' control-tpl="search"'
        .  ' placeholder="' . $t1 . '"'
        .  ' title="' . $t2 . '"'
        .  ' aria-label="' . $t1 . '"'
        .  ' class="input-field"'
        .  ' role="combobox"'
        .  ' accesskey="z"'
        .  ' autocapitalize="none"'
        .  ' autocomplete="off"'
        .  ' aria-autocomplete="list"'
        .  ' aria-expanded="false"'
        .  ' autocorrect="off"'
        .  ' spellcheck="false"'
        .  ' />'
        .  '<span class="input-icon" aria-hidden="true"><i class="fa-solid fa-magnifying-glass"></i></span>'
        . '</div>';

    // aria-owns="v-0" size="1">
    $widget->section([
        'content' => $html,
        'css' => 'layout-search'
    ]);
};
