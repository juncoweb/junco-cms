<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Database\Repository\Setter;
use Junco\Settings\PluginLoader;

return function (PluginLoader $loader) {
    $repository = new Setter;
    $adapters   = $repository->getAdapters();
    $collations = $repository->getCollations();

    $loader->setOptions('adapter', $adapters);
    $loader->setOptions('collation', $collations);
};
