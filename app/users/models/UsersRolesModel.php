<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;

class UsersRolesModel extends Model
{
    // vars
    protected $db = null;
    protected $role_id = 0;

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
        // data
        $this->filter(POST, [
            'role_id'            => 'id',
            'role_name'            => 'text|required',
            'role_description'    => 'text',
        ]);

        // extract
        $this->extract('role_id');

        // query
        if ($this->role_id) {
            $this->db->safeExec("UPDATE `#__users_roles` SET ?? WHERE id = ?", $this->data, $this->role_id);
        } else {
            $this->db->safeExec("INSERT INTO `#__users_roles` (??) VALUES (??)", $this->data);
        }
    }

    /**
     * Delete
     */
    public function delete()
    {
        // data
        $this->filter(POST, ['id' => 'id|array|required:abort']);

        // security
        if ($this->inUse($this->data['id'])) {
            throw new Exception(_t('The record can not be deleted because it is being used.'));
        }

        // query
        $this->db->safeExec("DELETE FROM `#__users_roles` WHERE id IN (?..)", $this->data['id']);
        $this->db->safeExec("DELETE FROM `#__users_roles_labels_map` WHERE role_id IN (?..)", $this->data['id']);
    }

    /**
     * 
     */
    protected function inUse(array $role_id): bool
    {
        return (bool)$this->db->safeFind("
		SELECT 
		 COUNT(*)
		FROM `#__users_roles_map`
		WHERE role_id IN (?..)", $role_id)->fetchColumn();
    }
}
