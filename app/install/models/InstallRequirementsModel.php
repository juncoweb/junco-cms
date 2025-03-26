<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class InstallRequirementsModel extends Model
{
    /**
     * Data
     */
    public function getData()
    {
        $config = config('extensions');
        $writables = [
            SYSTEM_STORAGE,
            SYSTEM_SETPATH,
        ];

        foreach ($writables as $i => $file) {
            $writables[$i] = [
                'is_writable' => is_writable($file),
                'file' => str_replace(SYSTEM_ABSPATH, '', $file),
            ];
        }

        return [
            'php_version' => PHP_VERSION,
            'min_php_version' => $config['extensions.min_php_version'],
            'max_php_version' => $config['extensions.max_php_version'],
            'min_php_version_result' => version_compare(PHP_VERSION, $config['extensions.min_php_version'], '>='),
            'max_php_version_result' => version_compare(PHP_VERSION, $config['extensions.max_php_version'], '<'),
            'db_support' => extension_loaded('mysqli'),
            'gd_support' => extension_loaded('gd'),
            'writables' => $writables
        ];
    }
}
