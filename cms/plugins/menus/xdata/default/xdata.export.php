<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Extensions\XData\XData;
use Junco\Menus\XStorage;

return function (XData $xdata) {
    $data = (new XStorage)->fetchAll($xdata->extension_id);

    return $xdata->putData($data);
};
