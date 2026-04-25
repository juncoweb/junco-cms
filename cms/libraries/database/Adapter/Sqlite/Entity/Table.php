<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Adapter\Sqlite\Entity;

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

    /**
     * Get
     * 
     * @return string
     */
    public function getCreateStatement(): string
    {
        $definition = [];

        if ($this->columns) {
            $definition[] = $this->getColumnsStatement($this->columns);
        }

        if ($this->indexes) {
            $definition[] = $this->getIndexesStatement($this->indexes);
        }

        if ($this->foreign_keys) {
            $definition[] = $this->getForeignKeysStatement($this->foreign_keys);
        }

        //
        $definition = implode(",\n  ", $definition);
        $options    = implode("  ", $this->getOptionsStatement());

        return "CREATE TABLE IF NOT EXISTS `$this->name` (\n  $definition\n) $options";
    }
}
