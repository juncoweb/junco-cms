<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Settings\PluginLoader;

return function (PluginLoader $loader) {
    $callback = function ($value) {
        if (!$value) {
            return '';
        }

        return implode('|', array_map(function ($partial) {
            return is_array($partial)
                ? implode('+', $partial)
                : $partial;
        }, $value));
    };

    $loader->setValue('delivery', $callback, true);
    $loader->setOptions('links', ['title', 'color', 'icon', 'url']);
};
