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
	(new AssetsExporter)->export(
		$xdata->basepath,
		$xdata->extension_aliases
	);
};
