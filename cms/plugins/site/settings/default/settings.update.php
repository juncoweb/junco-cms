<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

return function (&$rows) {
	if ($rows['url'] && substr($rows['url'], -1) != '/')
		$rows['url'] .= '/';
};
