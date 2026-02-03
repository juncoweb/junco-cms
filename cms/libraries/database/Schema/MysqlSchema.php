<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema;

use Junco\Database\Schema\Interface\SchemaInterface;
use Junco\Database\Schema\Mysql\DatabaseInfo;
use Junco\Database\Schema\Mysql\Columns;
use Junco\Database\Schema\Mysql\ForeignKeys;
use Junco\Database\Schema\Mysql\Indexes;
use Junco\Database\Schema\Mysql\Routines;
use Junco\Database\Schema\Mysql\Tables;
use Junco\Database\Schema\Mysql\Triggers;
use Database;

class MysqlSchema implements SchemaInterface
{
    //
    protected $db;
    protected $database_info;
    protected $columns;
    protected $foreignKeys;
    protected $indexes;
    protected $registers;
    protected $routines;
    protected $tables;
    protected $triggers;

    /**
     * Constructor
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * Get
     * 
     * @return array
     */
    public function getInfo(): array
    {
        return $this->db->getInfo();
    }

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
     * Tables
     * 
     * @return Tables
     */
    public function tables(): Tables
    {
        return $this->tables ??= new Tables($this->db);
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
