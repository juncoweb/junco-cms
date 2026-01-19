<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Settings\PluginLoader;

return function (PluginLoader $loader) {
    $loader->setSnippet('frontend_default_snippet', 'template');
    $loader->setSnippet('backend_default_snippet', 'template');
};
