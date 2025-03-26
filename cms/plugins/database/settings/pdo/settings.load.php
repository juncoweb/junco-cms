<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

return function (&$rows) {
    $rows['pdo_adapter']['options'] = [
        'pgsql'    => 'PostgreSQL',
        'mysql'    => 'MySql',
    ];
};
