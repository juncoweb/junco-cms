<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Settings\PluginLoader;

return function (PluginLoader $loader) {
    $loader->setOptions('availables', (new LanguageHelper)->getAvailables(true));
    $loader->setOptions('type', ['Cookie', 'URL']);

    if (!function_exists('gettext')) {
        $loader->setHelp('use_gettext', '<span class="color-red">' . _t('The gettext library is not available.') . '</span>');
    }
};
