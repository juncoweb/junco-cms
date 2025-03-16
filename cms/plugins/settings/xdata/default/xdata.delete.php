<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

/**
 * Delete
 *
 * @param object $xdata
 */
return function (&$xdata) {
	(new Settings($xdata->extension_alias))->deleteAll();
};
