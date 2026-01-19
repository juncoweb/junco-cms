<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Router\ReplacesHelper;
use Junco\Settings\PluginUpdater;

return function (PluginUpdater $updater) {
    $updater->setValue('route_replaces', fn($value) => (new ReplacesHelper)->after($value), '');
    $updater->setValue('access_points', function ($value) {
        $value = json_decode($value);

        if (!is_array($value)) {
            throw new Exception(_t('The access points have produced an error.'));
        }

        if (!in_array('admin', $value)) {
            $value[] = 'admin';
        }

        if (!in_array('front', $value)) {
            $value[] = 'front';
        }

        return $value;
    }, ['admin', 'front']);
};
