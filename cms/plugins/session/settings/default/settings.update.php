<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

return function (&$rows = false) {
    if ($rows['allow_safe_path']) {
        $dir = Session::SAFE_PATH;

        is_dir($dir) or mkdir($dir, SYSTEM_MKDIR_MODE);
    }
};
