<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Template\WidgetInterface;

return function (WidgetInterface $widget) {

    $_search = _t('Search');
    $url = router()->getUrlForm('/search');
    $html = '<form action="' . $url['action'] . '">'
        . $url['hidden']
        . FormSecurity::getToken()
        . '<div class="input-icon-group" style="max-width: 300px;">'
        . '<input type="text" name="q" class="input-field" title="' . $_search . '" aria-label="' . $_search . '" placeholder="' . $_search . '">'
        . '<button type="submit" title="' . $_search . '" class="input-icon"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i></button>'
        . '</div>'
        . '</form>';

    //
    $widget->section([
        'title' => $_search,
        'content' => $html,
        'css' => 'widget-search'
    ]);
};
