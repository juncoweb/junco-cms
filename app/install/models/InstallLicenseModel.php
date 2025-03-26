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
        $license = config('install.license');

        if (is_file(SYSTEM_ABSPATH . $license)) {
            $license = file_get_contents(SYSTEM_ABSPATH . $license);
        } else {
            $license = _t('Oops, they stole the license!');
        }

        return [
            'license' => $license,
        ];
    }
}
