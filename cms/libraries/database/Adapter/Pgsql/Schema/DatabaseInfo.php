<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Adapter\Pgsql\Schema;

use Junco\Database\Base\Schema\DatabaseInfoInterface;
use Junco\Database\Adapter\Mysql\Entity\DatabaseEntity;
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
         datname,
         datcollate,
         datctype
        FROM pg_database
        WHERE datname = current_database()")->fetch();

        return new DatabaseEntity(
            $data['datname'],
            $data['datcollate']
        );
    }

    /**
     * Get
     * 
     * @return array
     */
    public function getEngines(): array
    {
        return [];
    }

    /**
     * Get
     * 
     * @return array
     */
    public function getCollations(): array
    {
        return $this->db->query("
        SELECT
         collname
        FROM pg_catalog.pg_collation
        ORDER BY collname")->fetchAll(Database::FETCH_COLUMN, 'collname');
    }
}
