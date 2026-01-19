<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class UsersRolesModel extends Model
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
     * Save
     */
    public function save()
    {
        $data = $this->filter(POST, [
            'role_id'          => 'id',
            'role_name'        => 'text|required',
            'role_description' => 'text',
        ]);

        // slice
        $role_id = $this->slice($data, 'role_id');

        // query
        if ($role_id) {
            $this->db->exec("UPDATE `#__users_roles` SET ?? WHERE id = ?", $data, $role_id);
        } else {
            $this->db->exec("INSERT INTO `#__users_roles` (??) VALUES (??)", $data);
        }
    }

    /**
     * Delete
     */
    public function delete()
    {
        $data = $this->filter(POST, ['id' => 'id|array|required:abort']);

        // security
        if ($this->inUse($data['id'])) {
            return $this->unprocessable(_t('The record can not be deleted because it is being used.'));
        }

        // query
        $this->db->exec("DELETE FROM `#__users_roles` WHERE id IN (?..)", $data['id']);
        $this->db->exec("DELETE FROM `#__users_roles_labels_map` WHERE role_id IN (?..)", $data['id']);
    }

    /**
     * 
     */
    protected function inUse(array $role_id): bool
    {
        return (bool)$this->db->query("
		SELECT 
		 COUNT(*)
		FROM `#__users_roles_map`
		WHERE role_id IN (?..)", $role_id)->fetchColumn();
    }
}
