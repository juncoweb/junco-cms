<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Settings\PluginUpdater;

return function (PluginUpdater $updater) {
    $collation = $updater->getValue('collation');
    $collation and $updater->setValue('charset', fn($value) => substr($collation, 0, strpos($collation, '_')));
};
