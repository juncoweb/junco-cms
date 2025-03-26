<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema;

use Junco\Database\Schema\Interface\SchemaInterface;
use Junco\Database\Schema\Interface\_DatabaseInterface;
use Junco\Database\Schema\Interface\FieldsInterface;
use Junco\Database\Schema\Interface\ForeignKeysInterface;
use Junco\Database\Schema\Interface\IndexesInterface;
use Junco\Database\Schema\Interface\RegistersInterface;
use Junco\Database\Schema\Interface\RoutinesInterface;
use Junco\Database\Schema\Interface\TablesInterface;
use Junco\Database\Schema\Interface\TriggersInterface;
use Junco\Database\Schema\Mysql\_Database;
use Junco\Database\Schema\Mysql\Fields;
use Junco\Database\Schema\Mysql\ForeignKeys;
use Junco\Database\Schema\Mysql\Indexes;
use Junco\Database\Schema\Mysql\Registers;
use Junco\Database\Schema\Mysql\Routines;
use Junco\Database\Schema\Mysql\Tables;
use Junco\Database\Schema\Mysql\Triggers;
use Database;

/**
 * Database Schema Mysqli Adapter
 */
class MysqlSchema implements SchemaInterface
{
    //
    protected $db;
    protected $_database;
    protected $fields;
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
     * @return _DatabaseInterface
     */
    public function database(): _DatabaseInterface
    {
        return $this->_database ??= new _Database($this->db);
    }

    /**
     * Fields
     * 
     * @return FieldsInterface
     */
    public function fields(): FieldsInterface
    {
        return $this->fields ??= new Fields($this->db);
    }

    /**
     * ForeignKeys
     * 
     * @return ForeignKeysInterface
     */
    public function foreignKeys(): ForeignKeysInterface
    {
        return $this->foreignKeys ??= new ForeignKeys($this->db);
    }

    /**
     * Indexes
     * 
     * @return IndexesInterface
     */
    public function indexes(): IndexesInterface
    {
        return $this->indexes ??= new Indexes($this->db);
    }

    /**
     * Registers
     * 
     * @return RegistersInterface
     */
    public function registers(): RegistersInterface
    {
        return $this->registers ??= new Registers($this->db);
    }

    /**
     * Routines
     * 
     * @return RoutinesInterface
     */
    public function routines(): RoutinesInterface
    {
        return $this->routines ??= new Routines($this->db);
    }

    /**
     * Tables
     * 
     * @return TablesInterface
     */
    public function tables(): TablesInterface
    {
        return $this->tables ??= new Tables($this->db);
    }

    /**
     * Triggers
     * 
     * @return TriggersInterface
     */
    public function triggers(): TriggersInterface
    {
        return $this->triggers ??= new Triggers($this->db);
    }
}
