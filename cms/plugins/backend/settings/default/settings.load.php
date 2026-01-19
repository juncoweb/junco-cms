<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Settings\PluginLoader;

return function (PluginLoader $loader) {
    $loader->setOptions('theme', (new AssetsThemes)->scanAll());
    $loader->setOptions('header_color', [
        'default'   => 'Default',
        'primary'   => 'Primary',
        'secondary' => 'Secondary',
        'info'      => 'Info',
        'warning'   => 'Warning',
        'success'   => 'Success',
        'danger'    => 'Danger'
    ]);
    $loader->setPlugins('mainbar', 'widget');
    $loader->setPlugins('sidebar', 'widget');
};
