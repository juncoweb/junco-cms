<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Settings\PluginLoader;

return function (PluginLoader $loader) {
    $loader->setOptions('update_gap', [
        -1 => '--- ' . _t('Select') . ' ---',
        0  => 0,
        5  => 5,
        10 => 10,
        15 => 15,
        20 => 20,
        25 => 25,
        30 => 30,
        35 => 35,
        40 => 40,
    ]);
};
