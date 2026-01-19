<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Settings\PluginLoader;

return function (PluginLoader $loader) {
    $options = [
        'className',
        'shared'
    ];
    $loader->setOptions('system_registers', $options);
    $loader->setOptions('user_registers', $options);
};
