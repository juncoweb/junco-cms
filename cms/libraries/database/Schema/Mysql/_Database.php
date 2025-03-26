<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Mysql;

use Junco\Database\Schema\Interface\_DatabaseInterface;
use Database;

class _Database implements _DatabaseInterface
{
    //
    protected $db;

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
    public function getEngines(): array
    {
        return $this->db
            ->safeFind("SHOW ENGINES")
            ->fetchAll(\Database::FETCH_COLUMN, ['Engine' => 'Engine']);
    }

    /**
     * Get
     * 
     * @return array
     */
    public function getCollations(): array
    {
        return $this->db->safeFind("SHOW COLLATION")->fetchAll();
    }

    /**
     * Get
     *
     * @return array
     */
    public function showData(): array
    {
        $db_name = $this->db->safeFind("SELECT DATABASE()")->fetchColumn();
        $query = $this->db->safeFind("SHOW CREATE DATABASE `$db_name`")->fetchColumn(1);

        return  [
            'Name'            => $db_name,
            'MysqlQuery'    => $query,
        ];
    }
}
