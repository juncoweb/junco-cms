<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Base\Schema;

use Junco\Database\Base\Schema\DatabaseInfoInterface;
use Junco\Database\Base\Schema\ColumnsInterface;
use Junco\Database\Base\Schema\ForeignKeysInterface;
use Junco\Database\Base\Schema\IndexesInterface;
use Junco\Database\Base\Schema\RoutinesInterface;
use Junco\Database\Base\Schema\TablesInterface;
use Junco\Database\Base\Schema\TriggersInterface;
use Database;

abstract class SchemaBase implements SchemaInterface
{
    //
    protected Database              $db;
    protected DatabaseInfoInterface $database_info;
    protected TablesInterface       $tables;
    protected ColumnsInterface      $columns;
    protected ForeignKeysInterface  $foreignKeys;
    protected IndexesInterface      $indexes;
    protected RoutinesInterface     $routines;
    protected TriggersInterface     $triggers;

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
}
