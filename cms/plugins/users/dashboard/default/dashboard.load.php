<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

return function (&$dashboard) {
	$allow_cache = SYSTEM_ALLOW_CACHE;
	$html        = '';

	if ($allow_cache) {
		$cache_key	= 'usys-ha#';
		$cache		= cache();
		$html		= $cache->get($cache_key);
	}

	// cache
	if (!$html) {
		$reports = new UsersReport();
		$chart = '<div class="panel">'
			.   '<div class="panel-header"><h5>' . _t('Users') . '</h5></div>'
			.   '<div class="panel-body">'
			.      '<div data-chart="line" style="display: none;">' . json_encode($reports->getChartData()) . '</div>'
			.   '</div>'
			. '</div>';

		$data = $reports->getData();
		$details = '<h4>'
			. _t('Users')
			. ' (<span>' . $data['num_users'] . '</span>)'
			. '<a href="' . url('admin/users') . '"><i class="fa-solid fa-external-link float-right"></i></a>'
			. '</h4>'
			. '<span class="color-light">' . sprintf(_t('Last %s'), $data['created_at']->format(_t('Y-m-d'))) . '</span>';

		//
		$html = '<div class="grid grid-21 grid-responsive mb-4">'
			. '<div>' . $chart . '</div>'
			. '<div><div class="panel"><div class="panel-body">' . $details . '</div></div></div>'
			. '</div>';

		$allow_cache and $cache->set($cache_key, $html);
	}

	$dashboard->row($html);
};
