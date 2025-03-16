<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Assets\Compilation\ScssCompiler;
use Junco\Assets\Compilation\UrlFixer;

return function (&$rows) {
	$rows['precompile']['options'] = ScssCompiler::getOptions();
	$rows['fixurl']['options'] = UrlFixer::getOptions();
	$rows['cssmin_plugin']['plugin'] =
		$rows['jsmin_plugin']['plugin'] = 'minifier';
};
