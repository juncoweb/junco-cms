<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

$html = '<div id="settings-box" class="set-box"></div>';

// template
$tpl = Template::get();
$tpl->options([
    'js' => 'assets/settings-admin.min.js',
    'domready' => 'Settings.Load(\'' . $key . '\')',
    'thirdbar' => 'settings.thirdbar'
]);
$tpl->content($html);

return $tpl->response();
