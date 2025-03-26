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
    $xdata->is_installer or (new AssetsImporter)->import(
        $xdata->basepath,
        $xdata->extension_aliases
    );
};
