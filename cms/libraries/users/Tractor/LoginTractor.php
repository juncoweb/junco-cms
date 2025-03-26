<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Users\Tractor;

use Junco\Users\Exception\UserNotActiveException;
use Junco\Users\Exception\UserNotFoundException;
use Junco\Users\UserActivity;
use Junco\Users\UserActivityToken;
use Junco\Users\UserHelper;

class LoginTractor
{
    // vars
    protected $db;
    protected $activity;
    //
    protected int $user_id = 0;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = db();
        $this->activity = new UserActivity;
    }

    /**
     * Login
     * 
     * @param string $email_username
     * @param string $password
     * 
     * @throws UserNotFoundException
     * @throws UserNotActiveException
     * 
     * @return void
     */
    public function validateCredencial(string $email_username, string $password): void
    {
        if (!$email_username) {
            throw new UserNotFoundException(_t('Invalid email/username.'));
        }

        if (!$password) {
            throw new UserNotFoundException(_t('Please, fill in the password.'));
        }

        $config = config('users');

        // security
        $config['users.locks_level']
            and $this->activity->verify('login');

        $user = UserHelper::getUserFromInput($email_username);

        if (!$user) {
            $this->activity->record('login', -1, 0, ['enter' => $email_username]);
            throw new UserNotFoundException(_t('Invalid username/password'));
        }

        if ($user['status'] !== 'active') {
            $this->activity->record('login', $this->getActivityCode($user['status']), $user['id']);
            throw new UserNotActiveException();
        }

        if (!password_verify($password, $user['password'])) {
            $config['users.autologin']
                and $this->sendAutoLoginEmail($user);

            $this->activity->record('login', -7, $user['id']);
            throw new UserNotFoundException(_t('Invalid username/password'));
        }

        $config['users.password_rehash']
            and UserHelper::rehash($password, $user['id']);

        $this->user_id = $user['id'];
    }

    /**
     * Login
     */
    public function preLogin(bool $not_expire = false): array
    {
        if (!config('users.not_expire')) {
            $not_expire = false;
        }

        curuser()->preLogin($this->user_id, $not_expire, $data);

        return $data;
    }

    /**
     * Login
     */
    public function login(bool $not_expire = false): array
    {
        if (!config('users.not_expire')) {
            $not_expire = false;
        }

        if (curuser()->login($this->user_id, $not_expire, $data)) {
            $this->activity->record('login', 0, $this->user_id);
        }

        return $data;
    }

    /**
     * Get
     */
    public function getResponseData(): array
    {
        return $this->activity->getExpiresData();
    }

    /**
     * Get
     */
    protected function getActivityCode(string $status)
    {
        switch ($status) {
            case 'inactive':
                return -2;
            case 'autosignup':
                return -3;
        }

        abort();
    }

    /**
     * Send auto-login email
     */
    protected function sendAutoLoginEmail(array $user)
    {
        UserActivityToken::generateAndSend(
            'autologin',
            $user['id'],
            $user['email'],
            $user['fullname']
        );
    }
}
