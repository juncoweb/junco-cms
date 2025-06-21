<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Users;

use Junco\Users\UserHelper;
use Exception;
use Junco\Users\Enum\UserStatus;

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
    ) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception(_t('Please, fill in with a valid email.'));
        }

        // query
        $user = $this->db->safeFind("
		SELECT
		 id ,
		 fullname ,
		 username ,
		 email ,
		 status ,
		 ( FALSE ) AS is_created
		FROM `#__users`
		WHERE email = ?", $email)->fetch();

        if (!$user) {
            $role_id  = config('users.default_ucid') or abort();
            $username = $this->getValidUsername($email);
            $status   = UserStatus::autosignup;

            if (!$fullname) {
                $fullname = $username;
            }

            // query
            $this->db->safeExec("INSERT INTO `#__users` (??) VALUES (??)", [
                'fullname'       => $fullname,
                'username'       => $username,
                'email'          => $email,
                'verified_email' => $verified_email ? 'yes' : 'no',
                'status'         => $status
            ]);
            $user_id = $this->db->lastInsertId();

            // query - role
            $this->db->safeExec("INSERT INTO `#__users_roles_map` (user_id, role_id) VALUES (?, ?)", $user_id, $role_id);

            // Email
            if ($send_token) {
                UserActivityToken::generateAndSend('signup', $user_id, $email, $fullname);
            }

            $user = [
                'id'         => $user_id,
                'fullname'   => $fullname,
                'username'   => $username,
                'email'      => $email,
                'status'     => $status,
                'is_created' => true
            ];
        }

        return $user;
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
        return !$this->db->safeFind("SELECT COUNT(*) FROM `#__users` WHERE username = ?", $username)->fetchColumn();
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
