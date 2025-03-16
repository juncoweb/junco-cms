<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

return function (&$widget) {
	$widget->section([
		'content' => (new Samples)->menu('system')
	]);
};
