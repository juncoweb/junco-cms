<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Settings\PluginUpdater;

return function (PluginUpdater $updater) {
    $updater->setValue('locale', fn($value) => trim($value, '/\\'), '');
    $updater->setValue('use_gettext', fn($value) => function_exists('gettext'), false);
};
