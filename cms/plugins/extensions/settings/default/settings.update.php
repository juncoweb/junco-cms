<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Settings\PluginUpdater;

return function (PluginUpdater $updater) {
    $config   = config('extensions');
    $fs       = new Filesystem(SYSTEM_STORAGE);
    $sanitize = function ($value) use ($fs) {
        $fs->sanitizeDir($value, '/');
        return $value;
    };

    $updater->setValue('compiler_path', $sanitize);
    $updater->setValue('installer_path', $sanitize);

    if ($config['extensions.compiler_path'] != $updater->getValue('compiler_path')) {
        $fs->rename($config['extensions.compiler_path'], $updater->getValue('compiler_path'));
    }

    if ($config['extensions.installer_path'] != $updater->getValue('installer_path')) {
        $fs->rename($config['extensions.installer_path'], $updater->getValue('installer_path'));
    }
};
