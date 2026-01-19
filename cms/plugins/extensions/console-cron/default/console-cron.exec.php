<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Console\Cron;

return function (Cron $cron) {
    if (config('extensions-updater.enabled')) {
        $expression = config('extensions-updater.cron_expression');
        $expression and $cron->on($expression)?->call(function () {
            $model = new ExtensionsInstallerModel;
            $model->setData(['option' => 2])->findUpdates();
            $days = config('extensions-updater.update_gap');

            if ($days > -1) {
                $model->setData(['before_at' => $days * 24])->updateAll();
            }
        });
    }
};
