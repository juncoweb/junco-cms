<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Settings\PluginUpdater;

return function (PluginUpdater $updater) {
    if ($updater->getValue('use_background') && !config('jobs.worker')) {
        throw new Exception('You must configure the jobs before background processing.');
    }
};
