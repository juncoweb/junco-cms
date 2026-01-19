<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */


use Junco\Extensions\XData\XData;

return function (XData $xdata) {
    db()->exec("DELETE FROM `#__menus` WHERE extension_id = ?", $xdata->extension_id);
};
