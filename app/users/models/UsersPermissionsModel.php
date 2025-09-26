<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class UsersPermissionsModel extends Model
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
     * Status
     */
    public function status()
    {
        $data = $this->filter(POST, [
            'id'      => 'id|array|required:abort',
            'role_id' => 'id|required:abort',
            'status'  => 'int',
        ]);

        // security
        if ($this->isMyPermission($data['id'], $data['role_id'])) {
            return $this->unprocessable(_t('You cannot modify your administration permission.'));
        }

        if ($this->isDefaultRole($data['role_id'])) {
            return $this->unprocessable(_t('The default role cannot modify the administration permission.'));
        }

        $status = match ($data['status']) {
            1 => 0,
            2 => 1,
            default => "IF(status > 0, 0, 1)"
        };

        // query
        $stmt = $this->db->prepare("
		INSERT IGNORE INTO `#__users_roles_labels_map` (role_id, label_id, status)
		VALUES (?, ?, 1)
		ON DUPLICATE KEY UPDATE status = $status");

        foreach ($data['id'] as $label_id) {
            $this->db->exec($stmt, $data['role_id'], $label_id);
        }

        $cache_key = config('usys-system.permissions_q');
        if ($cache_key) {
            cache()->delete($cache_key);
        }
    }

    /**
     * Is
     */
    protected function isMyPermission(array $label_id, int $role_id): bool
    {
        $admin_label_id = L_SYSTEM_ADMIN;

        if (!in_array($admin_label_id, $label_id)) {
            return false;
        }

        $user_id = curuser()->getId();
        return !$this->db->query("
		SELECT COUNT(*)
		FROM `#__users_roles_labels_map`
		WHERE label_id = ?
		AND role_id IN (
			SELECT role_id
			FROM `#__users_roles_map`
			WHERE user_id = ?
			AND role_id <> ?
		)
		AND status = 1", $admin_label_id, $user_id, $role_id)->fetchColumn();
    }

    /**
     * Is
     */
    protected function isDefaultRole(int $role_id): bool
    {
        return $role_id == config('users.default_ucid');
    }
}
