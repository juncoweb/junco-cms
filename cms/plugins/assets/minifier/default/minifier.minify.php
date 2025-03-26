<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Assets\Compilation\Minifier;

return function ($target) {
    $minifier = new Minifier();
    $minifier->add($target);
    $minifier->minify($target);
};
