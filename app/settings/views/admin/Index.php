<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

// template
$tpl = Template::get();
$tpl->options([
	'js' => 'assets/settings-admin.min.js',
	'domready' => 'Settings.Load(\'' . $key . '\')',
	'thirdbar' => 'settings.thirdbar'
]);
$tpl->content = '<div id="settings-box" class="set-box"></div>';

return $tpl->response();
