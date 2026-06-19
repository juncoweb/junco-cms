<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Users\Repository;

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
    public function getRoles(array $options = []): array
    {
        return $this->db->query("
        SELECT
         id,
         role_name
        FROM `#__users_roles`
        WHERE id NOT IN (
            SELECT role_id
            FROM `#__users_roles_labels_map`
            WHERE label_id = ?
            AND status = 1
        )
        ORDER BY role_name", L_SYSTEM_ADMIN)->fetchAll(Database::FETCH_COLUMN, [0 => 1], $options);
    }
}
