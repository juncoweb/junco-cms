<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

return function (&$row) {
	if (isset($row['mkdir_mode'])) {
		$row['mkdir_mode'] = octdec($row['mkdir_mode']);
	}

	//
	if (
		isset($row['libraries'])
		&& !($row['libraries'] && is_file(SYSTEM_ABSPATH . $row['libraries']))
	) {
		throw new Exception('The libraries are not found');
	}

	//
	if (
		!empty($row['profiler'])
		&& empty($row['developer_mode'])
	) {
		$row['profiler'] = false;
	}

	//
	if (isset($row['log_path'])) {
		$fs = new Filesystem('');
		$fs->sanitizeDir($row['log_path'], '/');

		if (!$row['log_path']) {
			$row['log_path'] = 'logs/';
		}
	}
};
