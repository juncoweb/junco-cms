<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Extensions\XData\MalformedDataException;
use Junco\Extensions\XData\XData;
use Junco\Users\Repository\XStorage;

return function (XData $xdata) {
    $result = (new XStorage)->import(
        $xdata->getData(),
        $xdata->extension_id // During import, the extension_id is loaded from the getData method.
    );

    if (!$result) {
        throw new MalformedDataException();
    }
};
