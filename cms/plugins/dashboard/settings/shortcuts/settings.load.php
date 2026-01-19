<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Settings\PluginLoader;

return function (PluginLoader $loader) {
    $loader->setOptions('shortcuts_allow_cache', [
        -1 => _t('Inherit'),
        0  => _t('No'),
        1  => _t('Yes')
    ]);
};
