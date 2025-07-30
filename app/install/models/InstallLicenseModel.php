<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class InstallLicenseModel extends Model
{
    /**
     * Data
     */
    public function getData()
    {
        $file = SYSTEM_ABSPATH . config('install.license');
        $license = is_file($file)
            ? file_get_contents($file)
            : _t('Oops, they stole the license!');

        return [
            'license' => $license,
        ];
    }
}
