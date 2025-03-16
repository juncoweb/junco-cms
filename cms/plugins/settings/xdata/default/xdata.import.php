<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

/**
 * Import
 *
 * @param object $xdata
 *
 * @return void
 */
return function (&$xdata) {
	(new SettingsImporter($xdata->extension_alias))->import("{$xdata->basepath}storage/settings/");
};
