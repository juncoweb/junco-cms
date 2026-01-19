<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Extensions\XData\XData;
use Junco\Settings\Exporter;

return function (XData $xdata) {
    (new Exporter($xdata->extension_alias))
        ->export("{$xdata->basepath}storage/settings/", $xdata->is_installer);
};
