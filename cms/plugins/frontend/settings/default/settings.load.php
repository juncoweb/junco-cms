<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Settings\PluginLoader;

return function (PluginLoader $loader) {
    $themes = (new AssetsThemes)->scanAll();
    $styles = [
        '' => 'Default',
        'tpl-primary'   => 'Primary',
        'tpl-secondary' => 'Secondary',
        'tpl-info'      => 'Info',
        'tpl-success'   => 'Success',
        'tpl-warning'   => 'warning',
        'tpl-danger'    => 'Danger',
        //
        'tpl-solid' => 'Solid - Default',
        'tpl-solid tpl-primary'   => 'Solid - Primary',
        'tpl-solid tpl-secondary' => 'Solid - Secondary',
        'tpl-solid tpl-info'      => 'Solid - Info',
        'tpl-solid tpl-success'   => 'Solid - Success',
        'tpl-solid tpl-warning'   => 'Solid - warning',
        'tpl-solid tpl-danger'    => 'Solid - Danger',
    ];

    $loader->setOptions('theme', $themes);
    $loader->setOptions('print_theme', $themes);
    $loader->setPlugins('on_display', 'on_display');
    $loader->setOptions('header_style', $styles);
    $loader->setOptions('topbar', [
        'login'         => _t('Log in'),
        'theme'         => _t('Theme'),
        'language'      => _t('Language'),
        'notifications' => _t('Notifications'),
        'search'        => _t('Search'),
        'contact'       => _t('Contact')
    ]);
    $loader->setPlugin('navbar', 'widget');
    $loader->setPlugins('sidebar', 'widget');
    $loader->setPlugins('footer', 'widget');
    $loader->setOptions('footer_style', $styles);
    $loader->setOptions('copyright_style', $styles);
    $loader->setPlugin('cookie_consent', 'cookie-consent');
};
