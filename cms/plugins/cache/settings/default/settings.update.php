<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

return function (&$rows) {
    switch ($rows['adapter']) {
        case 'apcu':
            if (!function_exists('apcu_enabled') || !apcu_enabled()) {
                throw new Exception(_t('Your server does not support the selected library.'));
            }
            break;

        case 'memcached':
            if (!class_exists('Memcached')) {
                throw new Exception(_t('Your server does not support the selected library.'));
            }
            break;

        case 'redis':
            if (!class_exists('Redis')) {
                throw new Exception('Your server does not support the selected library.');
            }
            break;
    }
};
