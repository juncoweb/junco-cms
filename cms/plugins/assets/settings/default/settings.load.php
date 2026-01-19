<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Assets\Compilation\ScssCompiler;
use Junco\Assets\Compilation\UrlFixer;
use Junco\Settings\PluginLoader;

return function (PluginLoader $loader) {
    $loader->setOptions('precompile', ScssCompiler::getOptions());
    $loader->setOptions('fixurl', UrlFixer::getOptions());
    $loader->setPlugin('cssmin_plugin', 'minifier');
    $loader->setPlugin('jsmin_plugin', 'minifier');
};
