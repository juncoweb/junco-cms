<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

return function (&$rows) {
    $rows['adapter']['options'] = [
        'pdo'    => 'PDO',
        'mysql'    => 'MySql',
        'pgsql'    => 'PostgreSql',
    ];

    // collations
    $collations    = [];
    foreach (db()->getSchema()->database()->getCollations() as $row) {
        $collations[$row['Charset']][$row['Collation']]    = $row['Collation'];
    }

    $rows['collation']['options'] = $collations;
};
