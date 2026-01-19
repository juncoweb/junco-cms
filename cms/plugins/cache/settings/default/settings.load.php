<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Settings\PluginLoader;

return function (PluginLoader $loader) {
    $loader->setOptions('adapter', [
        'file'      => 'File',
        'apcu'      => 'APCu',
        'memcached' => 'Memcached',
        //'redis    => 'Redis',
        'null'      => 'Null',
    ]);
};
