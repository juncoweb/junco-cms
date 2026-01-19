<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Users\Service;

use Junco\Users\Entity\User;
use Junco\Users\Enum\ActivityType;
use Junco\Users\Enum\UserStatus;
use Junco\Users\Exception\UserNotActiveException;
use Junco\Users\Exception\UserNotFoundException;
use Junco\Users\UserActivity;
use Junco\Users\UserActivityToken;
use Junco\Users\UserHelper;
use Junco\Usys\UsysToken;

class Login
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
        $this->activity = new UserActivity(ActivityType::login);
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

        // security
        $this->activity->verify();

        $user = UserHelper::getUserFromInput($email_username);

        if (!$user) {
            $this->activity->record(UserActivity::USER_NOT_FOUND, 0, ['enter' => $email_username]);
            throw new UserNotFoundException(_t('Invalid username/password'));
        }

        if (!$user->isActive()) {
            $this->activity->record(UserActivity::USER_NOT_ACTIVE, $user->getId());
            throw new UserNotActiveException();
        }

        if (!$user->verifyPassword($password)) {
            config('users.autologin')
                and $this->sendAutoLoginEmail($user);

            $this->activity->record(UserActivity::INVALID_PASSWORD, $user->getId());
            throw new UserNotFoundException(_t('Invalid username/password'));
        }

        config('users.password_rehash')
            and UserHelper::rehash($password, $user->getId());

        $this->user_id = $user->getId();
    }

    /**
     * Set deferred log in
     * 
     * @param int    $user_id
     * @param bool   $remember
     * @param ?array &$data
     * 
     * @return bool  Returns false if an error occurs; otherwise returns true.
     */
    public function setDeferredLogin(bool $remember = false): array
    {
        if (!config('users.remember')) {
            $remember = false;
        }

        auth()->setDeferredLogin($this->user_id, $remember, $data);

        return $data;
    }

    /**
     * Login
     */
    public function login(bool $remember = false): array
    {
        if (!config('users.remember')) {
            $remember = false;
        }

        if (auth()->login($this->user_id, $remember, $data)) {
            $this->activity->record(UserActivity::OK, $this->user_id);
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
    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * Send auto-login email
     */
    protected function sendAutoLoginEmail(User $user): void
    {
        $token = UserActivityToken::generate(ActivityType::autologin, $user->getId(), $user->getEmail());
        (new UsysToken)->send($token, $user->getName());
    }
}
