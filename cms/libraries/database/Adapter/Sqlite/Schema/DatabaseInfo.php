<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Adapter\Sqlite\Schema;

use Junco\Database\Base\Schema\DatabaseInfoInterface;
use Junco\Database\Adapter\Sqlite\Entity\DatabaseEntity;
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
        $data = $this->db->getInfo();

        return new DatabaseEntity($data['file']);
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
        return [];
    }
}
