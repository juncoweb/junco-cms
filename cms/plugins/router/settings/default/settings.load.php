<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Router\ReplacesHelper;
use Junco\Settings\PluginLoader;

return function (PluginLoader $loader) {
    $components = db()->query("
	SELECT
	 extension_alias ,
	 extension_name
	FROM `#__extensions`
	WHERE components LIKE '%a%'
	ORDER BY extension_name, extension_alias")->fetchAll(Database::FETCH_COLUMN, [0 => 1], ['--- ' . _t('Select') . ' ---']);
    $helper = new ReplacesHelper;

    //
    $loader->setOptions('front_default_component', $components);
    $loader->setValue('route_replaces', fn($value) => $helper->before($value), true);
};
