<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
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
            ->query("SHOW ENGINES")
            ->fetchAll(\Database::FETCH_COLUMN, ['Engine' => 'Engine']);
    }

    /**
     * Get
     * 
     * @return array
     */
    public function getCollations(): array
    {
        return $this->db->query("SHOW COLLATION")->fetchAll();
    }

    /**
     * Get
     *
     * @return array
     */
    public function showData(): array
    {
        $db_name = $this->db->query("SELECT DATABASE()")->fetchColumn();
        $query = $this->db->query("SHOW CREATE DATABASE `$db_name`")->fetchColumn(1);

        return  [
            'Name'            => $db_name,
            'MysqlQuery'    => $query,
        ];
    }
}
