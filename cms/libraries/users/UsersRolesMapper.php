<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

class UsersRolesMapper
{
    // vars
    protected $db;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = db();
    }

    /**
     * Get
     * 
     * @param int $user_id
     * 
     * @return array
     */
    public function get(int $user_id): array
    {
        if ($user_id) {
            return $this->db->query("
			SELECT
			 m.role_id,
			 r.role_name
			FROM `#__users_roles` r
			LEFT JOIN `#__users_roles_map` m ON ( m.role_id = r.id )
			WHERE m.user_id = ?", $user_id)->fetchAll(Database::FETCH_COLUMN, [0 => 1]);
        }

        return [];
    }

    /**
     * Set
     * 
     * @param int $user_id
     * @param array $role_id
     * 
     * @return void
     */
    public function set(int $user_id, array $role_id): void
    {
        if (
            $user_id == curuser()->getId()
            && !$this->security($role_id)
        ) {
            return;
        }

        // query - delete
        $this->db->where("user_id = ?", $user_id);
        if ($role_id) {
            $this->db->where("role_id NOT IN (?..)", $role_id);
        }
        $this->db->exec("DELETE FROM `#__users_roles_map` [WHERE]");

        // query - insert
        if ($role_id) {
            $this->db->exec("INSERT IGNORE INTO `#__users_roles_map` (user_id, role_id)
			SELECT ?, id
			FROM `#__users_roles`
			WHERE id IN (?..)", $user_id, $role_id);
        }
    }

    /**
     * Security
     * 
     * @param array $role_id
     * 
     * @return bool
     */
    protected function security(array $role_id): bool
    {
        if (!$role_id) {
            return false;
        }

        return (bool)$this->db->query("
		SELECT COUNT(*)
		FROM `#__users_roles_labels_map`
		WHERE role_id IN (?..)
		AND label_id = ?
		AND status = 1", $role_id, L_SYSTEM_ADMIN)->fetchColumn();
    }
}
