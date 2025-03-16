<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

return function (&$rows) {
	$rows['mkdir_mode']['default_value'] = decoct($rows['mkdir_mode']['default_value']);
	$rows['mkdir_mode']['value'] = decoct($rows['mkdir_mode']['value']);

	// zones
	$zones = [];
	foreach (timezone_identifiers_list() as $zone) {
		$zones[$zone] = $zone;
	}


	//
	$rows['statement']['options'] = SystemHelper::getStatements();
	$rows['timezone']['options'] = $zones;
	$rows['default_editor']['plugin'] = 'editor';
};
