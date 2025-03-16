<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

return function (&$rows) {
	$rows['adapter']['options'] = [
		'file'		=> 'File',
		'apcu'		=> 'APCu',
		'memcached'	=> 'Memcached',
		//'redis'		=> 'Redis',
		'null'		=> 'Null',
	];
};
