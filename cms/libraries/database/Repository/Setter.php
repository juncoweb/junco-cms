<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Database\Repository;

use Database;

class Setter
{
    protected Database $db;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = db();
    }

    /**
     * Get
     */
    public function getAdapters(): array
    {
        return $this->db::getAdapters();
    }

    /**
     * Get
     */
    public function getCollations(array $options = []): array
    {
        foreach ($this->db->getSchema()->database()->getCollations() as $row) {
            $options[$row['Charset']][$row['Collation']] = $row['Collation'];
        }

        return $options;
    }
}
