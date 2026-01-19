<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Extensions\Installer\Installer;
use Junco\Mvc\Model;

class InstallExtensionsModel extends Model
{
    /**
     * Data
     */
    public function install()
    {
        (new Installer(true))->install();
    }
}
