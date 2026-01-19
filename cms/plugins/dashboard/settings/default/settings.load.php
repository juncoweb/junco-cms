<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Settings\PluginLoader;

return function (PluginLoader $loader) {
    $loader->setPlugins('myspace_plugins', 'dashboard');
    $loader->setSnippet('myspace_snippet', 'dashboard');

    $loader->setPlugins('admin_plugins', 'dashboard');
    $loader->setSnippet('admin_snippet', 'dashboard');
};
