<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

/**
 * Export
 *
 * @param object $xdata
 *
 * @return void
 */
return function (&$xdata) {
	(new SettingsExporter($xdata->extension_alias))->export("{$xdata->basepath}storage/settings/", $xdata->is_installer);
};
