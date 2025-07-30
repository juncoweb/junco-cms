<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Sqlite;

use Junco\Database\Schema\Interface\RoutinesInterface;
use Database;

class Routines implements RoutinesInterface
{
    //
    protected $db;
    //protected $prefixer;

    /**
     * Constructor
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        //$this->prefixer = $db->getPrefixer();
    }

    /**
     * Fetch
     * 
     * @param array $param
     * 
     * @return array
     */
    public function fetchAll(array $where = []): array
    {
        return [];
    }

    /**
     * Get
     * 
     * @param string $Type   The values can be FUNCTION or PROCEDURE.
     * @param string $Name
     */
    public function showData(string $Type = '', string $Name = '', array $db_prefix_tables = []): array
    {
        return [];
    }

    /**
     * Create
     * 
     * @param string $Type
     * @param string $RoutineName
     * @param array  $Routine
     * 
     * @return void
     */
    public function create(string $Type, string $RoutineName, array $Routine): void
    {
        //
    }

    /**
     * Drop
     * 
     * @param string $Type
     * @param string $RoutineName
     * 
     * @return void
     */
    public function drop(string $Type, string $RoutineName): int
    {
        return 0;
    }
}
