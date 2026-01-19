<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Settings\PluginLoader;

return function (PluginLoader $loader) {
    $zones = [];
    foreach (timezone_identifiers_list() as $zone) {
        $zones[$zone] = $zone;
    }

    $loader->setValue('mkdir_mode', fn($value) => decoct($value), true);
    $loader->setOptions('statement', SystemHelper::getStatements());
    $loader->setOptions('timezone', $zones);
    $loader->setPlugin('default_editor', 'editor');
};
