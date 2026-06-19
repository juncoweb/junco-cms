<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Router\ReplacesHelper;
use Junco\Router\Repository\Setter;
use Junco\Settings\PluginLoader;

return function (PluginLoader $loader) {
    $components = (new Setter)->getExtensions(['--- ' . _t('Select') . ' ---']);
    $helper = new ReplacesHelper;

    $loader->setOptions('front_default_component', $components);
    $loader->setValue('route_replaces', fn($value) => $helper->before($value), true);
};
