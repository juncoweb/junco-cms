<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Settings\PluginUpdater;

return function (PluginUpdater $updater) {
    $updater->setValue('delivery', function ($value) {
        $partials = explode('|', $value);
        $delivery = [];

        foreach ($partials as $partial) {
            $delivery[] = $partial
                ? explode('+', $partial)
                : [];
        }

        return $delivery;
    });
};
