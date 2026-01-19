<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Settings\PluginUpdater;

return function (PluginUpdater $updater) {
    $updater->setValue('mkdir_mode', fn($value) => octdec($value), 755);
    $updater->setValue('profiler', fn($value) => $updater->getValue('developer_mode'), false);
    $updater->setValue('log_path', function ($value) {
        (new Filesystem)->sanitizeDir($value, '/');
        return $value;
    }, 'logs/');
};
