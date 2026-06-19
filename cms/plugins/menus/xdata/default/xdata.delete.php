<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Extensions\XData\XData;
use Junco\Menus\XStorage;

return function (XData $xdata) {
    (new XStorage)->delete($xdata->extension_id);
};
