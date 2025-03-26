<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

return function (&$row = false) {
    $config = config('extensions');
    $fs = new Filesystem(SYSTEM_STORAGE);
    $fs->sanitizeDir($row['compiler_path'], '/');
    $fs->sanitizeDir($row['installer_path'], '/');

    if ($config['extensions.compiler_path'] != $row['compiler_path']) {
        $fs->rename($config['extensions.compiler_path'], $row['compiler_path']);
    }
    if ($config['extensions.installer_path'] != $row['installer_path']) {
        $fs->rename($config['extensions.installer_path'], $row['installer_path']);
    }
};
