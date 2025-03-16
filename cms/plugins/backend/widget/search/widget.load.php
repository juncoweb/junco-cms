<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

return function (&$widget) {
	$html = '<div class="input-icon-group">'
		.  '<input type="text" control-tpl="search" placeholder="' . ($t = _t('Search')) . '" title="' . $t . '" class="input-field" role="search" accesskey="z" autocomplete="off" autocorrect="off" spellcheck="false" />'
		.  '<span title="' . $t . '" class="input-icon"><i class="fa-solid fa-magnifying-glass"></i></span>'
		. '</div>';

	$widget->section([
		'content' => $html,
		'css' => 'layout-search'
	]);
};
