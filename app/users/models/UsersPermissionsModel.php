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
        // data
        $this->filter(POST, [
            'id'      => 'id|array|required:abort',
            'role_id' => 'id|required:abort',
            'status'  => 'int',
        ]);

        // security
        $this->security($this->data['id'], $this->data['role_id']);

        $status = match ($this->data['status']) {
            1 => 0,
            2 => 1,
            default => "IF(status > 0, 0, 1)"
        };

        // query
        $stmt = $this->db->prepare("
		INSERT IGNORE INTO `#__users_roles_labels_map` (role_id, label_id, status)
		VALUES (?, ?, 1)
		ON DUPLICATE KEY UPDATE status = $status");

        foreach ($this->data['id'] as $label_id) {
            $this->db->safeExec($stmt, $this->data['role_id'], $label_id);
        }

        $cache_key = config('usys-system.permissions_q');
        if ($cache_key) {
            cache()->delete($cache_key);
        }
    }

    /**
     * Security
     */
    protected function security(array $label_id, int $role_id): void
    {
        $admin_label_id = L_SYSTEM_ADMIN;

        if (!in_array($admin_label_id, $label_id)) {
            return;
        }

        // Administration permission
        $user_id = curuser()->id;
        $total = $this->db->safeFind("
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

        if (!$total) {
            throw new Exception(_t('You cannot modify your administration permission.'));
        }

        // Default Role
        if ($role_id == config('users.default_ucid')) {
            throw new Exception(_t('The default role cannot modify the administration permission.'));
        }
    }
}
