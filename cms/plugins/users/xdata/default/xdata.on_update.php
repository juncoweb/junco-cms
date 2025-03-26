<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Users\LabelsCache;

/**
 * On update
 *
 * @param object $xdata
 *
 * @return void
 */
return function (&$xdata) {
    (new LabelsCache)->update();
};
