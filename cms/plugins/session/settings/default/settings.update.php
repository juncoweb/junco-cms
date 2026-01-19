<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Settings\PluginUpdater;

return function (PluginUpdater $updater) {
    if ($updater->getValue('allow_safe_path')) {
        $dir = Session::SAFE_PATH;

        is_dir($dir) or mkdir($dir, SYSTEM_MKDIR_MODE);
    }
};
