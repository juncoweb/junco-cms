<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Adapter\Pgsql\Schema;

use Junco\Database\Base\Schema\SchemaBase;

class Schema extends SchemaBase
{
    /**
     * Database
     * 
     * @return DatabaseInfo
     */
    public function database(): DatabaseInfo
    {
        return $this->database_info ??= new DatabaseInfo($this->db);
    }

    /**
     * Tables
     * 
     * @return Tables
     */
    public function tables(): Tables
    {
        return $this->tables ??= new Tables($this->db);
    }

    /**
     * Columns
     * 
     * @return Columns
     */
    public function columns(): Columns
    {
        return $this->columns ??= new Columns($this->db);
    }

    /**
     * ForeignKeys
     * 
     * @return ForeignKeys
     */
    public function foreignKeys(): ForeignKeys
    {
        return $this->foreignKeys ??= new ForeignKeys($this->db);
    }

    /**
     * Indexes
     * 
     * @return Indexes
     */
    public function indexes(): Indexes
    {
        return $this->indexes ??= new Indexes($this->db);
    }

    /**
     * Routines
     * 
     * @return Routines
     */
    public function routines(): Routines
    {
        return $this->routines ??= new Routines($this->db);
    }

    /**
     * Triggers
     * 
     * @return Triggers
     */
    public function triggers(): Triggers
    {
        return $this->triggers ??= new Triggers($this->db);
    }
}
