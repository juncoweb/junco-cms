<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Adapter\Mysql\Entity;

use Junco\Database\Base\Entity\IndexBase;
use Error;

class Index extends IndexBase
{
    /**
     * Add
     * 
     * @return IndexColumnInterface
     */
    public function addColumn(string $name, int $sequence = 0, ?string $comment = null): IndexColumn
    {
        if (!$sequence) {
            $sequence = count($this->columns) + 1;
        }

        return $this->columns[] = new IndexColumn($name, $sequence, $comment);
    }
}
