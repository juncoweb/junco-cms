<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Extensions\XData\XData;
use Junco\Users\Repository\XStorage;

return function (XData $xdata) {
    $data = (new XStorage)->fetchAll($xdata->extension_id);

    return $xdata->putData($data);
};
