<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Filesystem\UploadedFileManager;
use Junco\Settings\PluginLoader;

return function (PluginLoader $loader) {
    $quality = [
        -1 => 'Default',
        0 => '0 - Worst quality (smaller file)',
        10 => '10 - Low quality',
        20 => '20 - Low quality',
        30 => '30 - Low quality',
        40 => '40 - Medium quality',
        50 => '50 - Medium quality',
        60 => '60 - Medium quality',
        70 => '70 - Hight quality',
        80 => '80 - Hight quality',
        90 => '90 - Hight quality',
        100 => '100 - Best quality (biggest file)',
    ];

    $loader->setOptions('default_rename', UploadedFileManager::getRenames());
    $loader->setOptions('accept_images', [
        'jpg'  => 'jpg',
        'jpeg' => 'jpeg',
        'gif'  => 'gif',
        'png'  => 'png',
        'webp' => 'webp',
    ]);
    $loader->setOptions('jpeg_quality', $quality);
    $loader->setOptions('webp_quality', $quality);
    $loader->setOptions('png_quality', [
        -1 => 'Default',
        0 => '0 - No compression',
        1 => '1 - Hight quality (biggest file)',
        2 => '2 - Hight quality',
        3 => '3 - Hight quality',
        4 => '4 - Medium quality',
        5 => '5 - Medium quality',
        6 => '6 - Medium quality',
        7 => '7 - Low quality',
        8 => '8 - Low quality',
        9 => '9 - Low quality (smaller file)',
    ]);
};
