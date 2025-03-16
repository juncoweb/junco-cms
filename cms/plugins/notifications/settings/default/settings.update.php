<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

return function (&$row) {
	if ($row['use_background'] && !config('jobs.worker')) {
		throw new Exception('You must configure the jobs before background processing.');
	}
};
