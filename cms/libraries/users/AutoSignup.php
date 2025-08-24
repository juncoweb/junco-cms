<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Users;

use Junco\Users\Entity\User;
use Junco\Users\Enum\UserStatus;
use Junco\Users\Enum\ActivityType;
use Junco\Users\UserHelper;
use Junco\Usys\UsysToken;
use Exception;

class AutoSignup
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
     * Get user
     */
    public function getUserFromEmail(
        string $email,
        string $fullname = '',
        bool   $send_token = false,
        bool   $verified_email = false
    ): User {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception(_t('Please, fill in with a valid email.'));
        }

        // query
        $data = $this->db->query("
		SELECT
		 id ,
		 fullname ,
		 username ,
		 email ,
         password ,
		 status
		FROM `#__users`
		WHERE email = ?", $email)->fetch();

        if ($data) {
            return new User(
                $data['id'],
                $data['username'],
                $data['fullname'],
                $data['email'],
                $data['password'],
                $data['status']
            );
        }

        return $this->newUser($email, $fullname, $send_token, $verified_email);
    }

    /**
     * 
     */
    protected function newUser(
        string $email,
        string $fullname,
        bool $send_token,
        bool $verified_email
    ): User {
        $role_id        = config('users.default_ucid') or abort();
        $username       = $this->getValidUsername($email);
        $status         = UserStatus::autosignup;
        $verified_email = $verified_email ? 'yes' : 'no';

        if (!$fullname) {
            $fullname = $username;
        }

        // query
        $this->db->exec("
        INSERT INTO `#__users` (
         fullname,
         username,
         email,
         verified_email, 
         status
        ) VALUES (?, ?, ?, ?, ?)", $fullname, $username, $email, $verified_email, $status);
        $user_id = $this->db->lastInsertId();

        // query
        $this->db->exec("INSERT INTO `#__users_roles_map` (user_id, role_id) VALUES (?, ?)", $user_id, $role_id);

        // Email
        if ($send_token) {
            $token = UserActivityToken::generate(ActivityType::signup, $user_id, $email);
            (new UsysToken)->send($token, $fullname);
        }

        return (new User($user_id, $username, $fullname, $email, '', $status))->setCreation();
    }

    /**
     * Get
     */
    protected function getValidUsername(string $email)
    {
        $username = substr($email, 0, strpos($email, '@'));

        if (!UserHelper::validateUsername($username, false) || !$this->isUniqueUsername($username)) {
            $username = $this->generateUsernameFromIncrement();

            if (!$this->isUniqueUsername($username)) {
                $username = $this->generateUsername();
            }
        }

        return $username;
    }

    /**
     * Unique
     */
    protected function isUniqueUsername(string $username)
    {
        return !$this->db->query("SELECT COUNT(*) FROM `#__users` WHERE username = ?", $username)->fetchColumn();
    }

    /**
     * Generate Username From Increment
     */
    protected function generateUsernameFromIncrement()
    {
        $number = $this->db->getSchema()->tables()->fetchAll(['Name' => 'users'])[0]['Auto_increment'] ?? 0;
        return "user{$number}";
    }

    /**
     * Generate Username
     */
    protected function generateUsername()
    {
        while (true) {
            $username = uniqid('user_');
            if ($this->isUniqueUsername($username)) {
                return $username;
            }
        }
    }
}
