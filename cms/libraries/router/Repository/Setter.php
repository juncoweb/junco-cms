<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Router\Repository;

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
    public function getExtensions(array $options): array
    {
        return $this->db->query("
        SELECT
         extension_alias ,
         extension_name
        FROM `#__extensions`
        WHERE components LIKE '%a%'
        ORDER BY extension_name, extension_alias")->fetchAll(Database::FETCH_COLUMN, [0 => 1], $options);
    }
}
