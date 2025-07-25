<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Users\Tractor;

use Junco\Users\Enum\UserStatus;
use Junco\Users\Exception\UserValidationException;
use Junco\Users\UserActivityToken;
use Junco\Users\UserHelper;

class SignupTractor
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
     * Signup
     */
    public function signup(
        string $fullname,
        string $username,
        string $email,
        string $password,
        string $verified,
        bool   $legal = true,
        int    $user_id = 0
    ): bool {
        // vars
        $role_id = config('users.default_ucid') or abort();

        // validate
        if (config('users.legal') && !$legal) {
            throw new UserValidationException(_t('You must accept the terms and conditions to register.'));
        }

        if ($password !== $verified) {
            throw new UserValidationException(_t('Passwords do not match.'));
        }

        if (!$fullname) {
            throw new UserValidationException(_t('Please, fill in the name.'));
        }

        UserHelper::validateUsername($username);
        UserHelper::validatePassword($password);

        if (!($user_id > 0)) {
            if (!$email) {
                throw new UserValidationException(_t('Your email does not pass the validity check.') . ' ' . _t('Please check it and if the problem persists, contact the administration.'));
            }

            UserHelper::isUniqueEmail($email);
        }

        // username
        UserHelper::isUniqueUsername($username, $user_id);

        //
        $password = UserHelper::hash($password);

        if ($user_id > 0) {
            $this->db->safeExec("
			UPDATE `#__users` 
			SET fullname = ?, username = ?, password = ?, status = ?
			WHERE id = ?", $fullname, $username, $password, UserStatus::active, $user_id);

            return true;
        }

        // query - insert
        $this->db->safeExec("INSERT INTO `#__users` (fullname, username, email, password) VALUES (?, ?, ?, ?)", $fullname, $username, $email, $password);
        $user_id = $this->db->lastInsertId();

        // query - role
        $this->db->safeExec("INSERT INTO `#__users_roles_map` (user_id, role_id) VALUES (?, ?)", $user_id, $role_id);

        // token
        $result = UserActivityToken::generateAndSend('activation', $user_id, $email, $fullname);

        if (!$result) {
            throw new UserValidationException(_t('Your account has been created correctly. However, an error occurred when sending the activation message.'));
        }

        return false;
    }
}
