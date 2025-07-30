<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;
use Junco\Users\UserHelper;

class UsersModel extends Model
{
    // vars
    protected $db;
    protected int   $user_id = 0;
    protected array $role_id = [];

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
            'user_id'  => 'id',
            'fullname' => 'text',
            'username' => '',
            'password' => '',
            'email'    => 'email',
            'role_id'  => 'id|array',
        ]);

        // extract
        $this->extract('user_id', 'role_id');

        // validate
        if (!$this->data['fullname']) {
            return $this->unprocessable(_t('Please, fill in the name.'));
        }

        if (!$this->data['username']) {
            return $this->unprocessable(_t('Please, fill in the username.'));
        }
        UserHelper::validateUsername($this->data['username']);

        // password
        if ($this->data['password']) {
            UserHelper::validatePassword($this->data['password']);
            $this->data['password'] = UserHelper::hash($this->data['password']);
        } elseif ($this->user_id) {
            unset($this->data['password']);
        } else {
            return $this->unprocessable(_t('Please, fill in the password.'));
        }

        // username
        UserHelper::isUniqueUsername($this->data['username'], $this->user_id);

        // email
        if ($this->data['email']) {
            UserHelper::isUniqueEmail($this->data['email'], $this->user_id);
        } elseif ($this->user_id) {
            unset($this->data['email']);
        } else {
            return $this->unprocessable(_t('Please, fill in with a valid email.'));
        }

        // query
        if ($this->user_id) {
            $this->db->exec("UPDATE `#__users` SET ?? WHERE id = ?", $this->data, $this->user_id);
        } else {
            $this->db->exec("INSERT INTO `#__users` (??) VALUES (??)", $this->data);
            $this->user_id = $this->db->lastInsertId();
        }

        (new UsersRolesMapper)->set($this->user_id, $this->role_id);
    }

    /**
     * Status
     */
    public function status()
    {
        // data
        $this->filter(POST, [
            'id'     => 'id|array|required:abort',
            'status' => 'enum:users.user_status',
        ]);

        // validate
        if ($this->isCurUser($this->data['id'])) {
            return $this->unprocessable(_t('Your account is not editable.'));
        }

        // query
        if ($this->data['status']) {
            $this->db->exec("UPDATE `#__users` SET status = ? WHERE id IN (?..)", $this->data['status'], $this->data['id']);
        } else {
            $this->db->exec("
            UPDATE `#__users`
            SET status = IF(status = 'active', 'inactive', 'active')
            WHERE id IN (?..)", $this->data['id']);
        }
    }

    /**
     * Delete
     */
    public function delete()
    {
        // data
        $this->filter(POST, ['id' => 'id|array|required:abort']);

        // query
        $this->db->exec("DELETE FROM `#__users_roles_map` WHERE user_id IN (?..)", $this->data['id']);
        $this->db->exec("DELETE FROM `#__users` WHERE id IN (?..)", $this->data['id']);
    }

    /**
     * Is
     */
    protected function isCurUser(array $user_id): bool
    {
        return in_array(curuser()->id, $user_id);
    }
}
