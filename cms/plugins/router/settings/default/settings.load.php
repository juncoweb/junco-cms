<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Router\ReplacesHelper;

return function (&$rows) {
    $rows['front_default_component']['options'] = db()->safeFind("
	SELECT
	 extension_alias ,
	 extension_name
	FROM `#__extensions`
	WHERE components LIKE '%a%'
	ORDER BY extension_name, extension_alias")->fetchAll(Database::FETCH_COLUMN, [0 => 1], ['--- ' . _t('Select') . ' ---']);

    (new ReplacesHelper)
        ->before($rows['route_replaces']['value'])
        ->before($rows['route_replaces']['default_value']);
};
