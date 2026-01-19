<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Settings\PluginUpdater;

return function (PluginUpdater $updater) {
    $updater->setValue('url', function ($value) {
        if (substr($value, -1) != '/') {
            $value .= '/';
        }

        return $value;
    });
};
