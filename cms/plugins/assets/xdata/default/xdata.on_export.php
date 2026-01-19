<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */


use Junco\Extensions\XData\XData;

return function (XData $xdata) {
    (new AssetsExporter)->export(
        $xdata->basepath,
        $xdata->extension_aliases
    );
};
