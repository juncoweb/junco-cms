<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Console\Cron;
use Junco\Settings\PluginUpdater;

return function (PluginUpdater $updater) {
    if (!(new Cron)->validate($updater->getValue('cron_expression'))) {
        throw new Exception(_t('The CRON expression is invalid.'));
    }

    $cron_plugins = Utils::arrayToggle(config('console.cron_plugins'), 'extensions', (bool)$updater->getValue('enabled'));

    (new Settings('console'))->update(['cron_plugins' => $cron_plugins]);
};
