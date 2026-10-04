<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;
use Junco\Users\Enum\UserStatus;
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
        $data = $this->filter(POST, [
            'user_id'  => 'id',
            'user_name' => 'text',
            'user_username' => '',
            'user_password' => '',
            'user_email'    => 'email',
            'role_id'       => 'id|array',
        ]);

        // slice
        $user_id = $this->slice($data, 'user_id');
        $role_id = $this->slice($data, 'role_id');

        // validate
        if (!$data['user_name']) {
            return $this->unprocessable(_t('Please, fill in the name.'));
        }

        if (!$data['user_username']) {
            return $this->unprocessable(_t('Please, fill in the username.'));
        }
        UserHelper::validateUsername($data['user_username']);

        // password
        if ($data['user_password']) {
            UserHelper::validatePassword($data['user_password']);
            $data['user_password'] = UserHelper::hash($data['user_password']);
        } elseif ($user_id) {
            unset($data['user_password']);
        } else {
            return $this->unprocessable(_t('Please, fill in the password.'));
        }

        // username
        UserHelper::isUniqueUsername($data['user_username'], $user_id);

        // email
        if ($data['user_email']) {
            UserHelper::isUniqueEmail($data['user_email'], $user_id);
        } elseif ($user_id) {
            unset($data['user_email']);
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
            $sql = UserStatus::toggle();

            $this->db->exec("UPDATE `#__users` SET status = $sql WHERE id IN (?..)", $data['id']);
        }
    }

    /**
     * Delete
     */
    public function delete()
    {
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
