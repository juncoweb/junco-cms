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
        $data = $this->filter(POST, [
            'user_id'  => 'id',
            'fullname' => 'text',
            'username' => '',
            'password' => '',
            'email'    => 'email',
            'role_id'  => 'id|array',
        ]);

        // slice
        $user_id = $this->slice($data, 'user_id');
        $role_id = $this->slice($data, 'role_id');

        // validate
        if (!$data['fullname']) {
            return $this->unprocessable(_t('Please, fill in the name.'));
        }

        if (!$data['username']) {
            return $this->unprocessable(_t('Please, fill in the username.'));
        }
        UserHelper::validateUsername($data['username']);

        // password
        if ($data['password']) {
            UserHelper::validatePassword($data['password']);
            $data['password'] = UserHelper::hash($data['password']);
        } elseif ($user_id) {
            unset($data['password']);
        } else {
            return $this->unprocessable(_t('Please, fill in the password.'));
        }

        // username
        UserHelper::isUniqueUsername($data['username'], $user_id);

        // email
        if ($data['email']) {
            UserHelper::isUniqueEmail($data['email'], $user_id);
        } elseif ($user_id) {
            unset($data['email']);
        } else {
            return $this->unprocessable(_t('Please, fill in with a valid email.'));
        }

        // query
        if ($user_id) {
            $this->db->exec("UPDATE `#__users` SET ?? WHERE id = ?", $data, $user_id);
        } else {
            $this->db->exec("INSERT INTO `#__users` (??) VALUES (??)", $data);
            $user_id = $this->db->lastInsertId();
        }

        (new UsersRolesMapper)->set($user_id, $role_id);
    }

    /**
     * Status
     */
    public function status()
    {
        // data
        $data = $this->filter(POST, [
            'id'     => 'id|array|required:abort',
            'status' => 'enum:users.user_status',
        ]);

        // validate
        if ($this->isCurUser($data['id'])) {
            return $this->unprocessable(_t('Your account is not editable.'));
        }

        // query
        if ($data['status']) {
            $this->db->exec("UPDATE `#__users` SET status = ? WHERE id IN (?..)", $data['status'], $data['id']);
        } else {
            $this->db->exec("
            UPDATE `#__users`
            SET status = IF(status = 'active', 'inactive', 'active')
            WHERE id IN (?..)", $data['id']);
        }
    }

    /**
     * Delete
     */
    public function delete()
    {
        // data
        $data = $this->filter(POST, ['id' => 'id|array|required:abort']);

        // query
        $this->db->exec("DELETE FROM `#__users_roles_map` WHERE user_id IN (?..)", $data['id']);
        $this->db->exec("DELETE FROM `#__users` WHERE id IN (?..)", $data['id']);
    }

    /**
     * Is
     */
    protected function isCurUser(array $user_id): bool
    {
        return in_array(curuser()->getId(), $user_id);
    }
}
