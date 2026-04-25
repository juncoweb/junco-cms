<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Adapter\Pgsql\Entity;

use Junco\Database\Base\Entity\TableBase;

class Table extends TableBase
{
    /**
     * Get
     * 
     * @return ColumnInterface
     */
    public function addColumn(string $name, ?string $type = null): Column
    {
        return $this->columns[] = new Column($this->name, $name, $type);
    }

    /**
     * Add
     * 
     * @param string $name
     * 
     * @return IndexInterface
     */
    public function addIndex(string $name): Index
    {
        return $this->indexes[] = new Index($this->name, $name);
    }
}
