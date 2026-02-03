<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Schema\Mysql;

use Junco\Database\Schema\Interface\DatabaseInfoInterface;
use Junco\Database\Schema\Mysql\Entity\DatabaseEntity;
use Database;

class DatabaseInfo implements DatabaseInfoInterface
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
     * Fetch
     *
     * @return ?DatabaseEntityInterface
     */
    public function fetch(): ?DatabaseEntity
    {
        $data = $this->db->query("
        SELECT
         SCHEMA_NAME ,
         DEFAULT_COLLATION_NAME
        FROM INFORMATION_SCHEMA.SCHEMATA
        WHERE SCHEMA_NAME = DATABASE()")->fetch();

        return new DatabaseEntity(
            $data['SCHEMA_NAME'],
            $data['DEFAULT_COLLATION_NAME']
        );
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
}
