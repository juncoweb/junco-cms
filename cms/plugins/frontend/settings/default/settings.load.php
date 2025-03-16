<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

return function (&$rows) {
	$rows['theme']['options'] =
		$rows['print_theme']['options'] = (new AssetsThemes)->scanAll();
	$rows['on_display']['plugins'] = 'on_display';
	//
	$rows['topbar']['options']	= [
		'login' => _t('Log in'),
		'theme' => _t('Theme'),
		'language' => _t('Language'),
		'notifications' => _t('Notifications'),
		'search' => _t('Search'),
		'contact' => _t('Contact')
	];
	$rows['navbar']['plugin']	= 'widget';
	$rows['sidebar']['plugins'] = 'widget';
	$rows['footer']['plugins']	= 'widget';
};
