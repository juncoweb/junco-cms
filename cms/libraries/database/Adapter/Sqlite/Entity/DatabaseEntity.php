<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Adapter\Sqlite\Entity;

use Junco\Database\Base\Entity\DatabaseBase;

class DatabaseEntity extends DatabaseBase
{
    /**
     * Get
     * 
     * @return string
     */
    public function getCreateStatement(): string
    {
        return "";
    }
}
