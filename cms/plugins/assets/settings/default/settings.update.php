<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

return function (&$row) {
	(new AssetsBasic)->updateVersion((bool)$row['version_control']);
};
