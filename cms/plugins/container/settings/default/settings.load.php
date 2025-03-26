<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Filesystem\ImageHelper;

return function (&$rows) {
    $rows['system_registers']['options'] =
        $rows['user_registers']['options'] = ['className', 'shared'];
};
