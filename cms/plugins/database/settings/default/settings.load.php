<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Settings\PluginLoader;

return function (PluginLoader $loader) {
    $loader->setOptions('adapter', [
        'pdo'   => 'PDO',
        'mysql' => 'MySql',
        'pgsql' => 'PostgreSql',
    ]);

    // collations
    $collations = [];
    foreach (db()->getSchema()->database()->getCollations() as $row) {
        $collations[$row['Charset']][$row['Collation']]    = $row['Collation'];
    }

    $loader->setOptions('collation', $collations);
};
