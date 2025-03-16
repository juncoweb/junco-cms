<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Console\Cron;
use Junco\Jobs\Jobs;

return function (Cron &$cron) {
	$cron->on('* * * * *')?->call(function () {
		(new Jobs)->run();
	});
};
