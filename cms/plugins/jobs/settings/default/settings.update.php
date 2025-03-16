<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

return function (&$row) {
	$cron_plugins = config('console.cron_plugins');
	$is_cron      = ($row['worker'] == 'cron');

	if ($is_cron != in_array('jobs', $cron_plugins)) {
		if ($is_cron) {
			$cron_plugins[] = 'jobs';
		} else {
			unset($cron_plugins[array_search('jobs', $cron_plugins)]);
		}

		(new Settings('console'))->update(['cron_plugins' => $cron_plugins]);
	}
};
