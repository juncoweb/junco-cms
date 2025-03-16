<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Router\ReplacesHelper;

return function (&$rows) {
	(new ReplacesHelper)->after($rows['route_replaces']);

	$rows['access_points'] = json_decode($rows['access_points']);

	if (!is_array($rows['access_points'])) {
		throw new Exception(_t('The access points have produced an error.'));
	}

	if (!in_array('admin', $rows['access_points'])) {
		$rows['access_points'][] = 'admin';
	}

	if (!in_array('front', $rows['access_points'])) {
		$rows['access_points'][] = 'front';
	}
};
