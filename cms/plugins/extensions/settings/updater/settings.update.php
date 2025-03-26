<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Console\Cron;

return function (&$row = false) {
    if (!(new Cron)->validate($row['cron_expression'])) {
        throw new Exception(_t('The CRON expression is invalid.'));
    }

    $cron_plugins = Utils::arrayToggle(
        config('console.cron_plugins'),
        'extensions',
        (bool)$row['enabled']
    );

    (new Settings('console'))->update(['cron_plugins' => $cron_plugins]);
};
