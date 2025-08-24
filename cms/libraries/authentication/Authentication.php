<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Authentication\GuardInterface;

class Authentication
{
    // vars
    protected $guard;

    /**
     * Constructor
     */
    public function __construct(?GuardInterface $guard = null)
    {
        $this->guard = $guard ?? $this->getGuard();
    }

    /**
     * Returns the user ID retrieved from storage.
     *
     * @return int
     */
    public function getUserId(): int
    {
        return $this->guard->getUserId();
    }

    /**
     * Returns the user ID retrieved from storage.
     *
     * @return int
     */
    public function getDeferredUserId(): int
    {
        return $this->guard->getDeferredUserId();
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
    public function setDeferredLogin(int $user_id = 0, bool $remember = false, ?array &$data = null): bool
    {
        $data ??= [];
        return $this->guard->setDeferredLogin($user_id, $remember, $data);
    }

    /**
     * Execute a deferred login.
     * 
     * @param ?array &$data
     * 
     * @return bool  Returns false if an error occurs; otherwise returns true.
     */
    public function execDeferredLogin(?array &$data = null): bool
    {
        $data ??= [];
        return $this->guard->execDeferredLogin($data);
    }

    /**
     * Login
     * 
     * @param int    $user_id       If it is zero, log out.
     * @param bool   $remember
     * @param ?array &$data
     * 
     * @return bool  Returns false if an error occurs; otherwise returns true.
     */
    public function login(int $user_id = 0, bool $remember = false, ?array &$data = null): bool
    {
        $data ??= [];
        return $this->guard->login($user_id, $remember, $data);
    }

    /**
     * Logout
     * 
     * @return bool  Returns false if an error occurs; otherwise returns true.
     */
    public function logout(): bool
    {
        return $this->guard->logout();
    }

    /**
     * Get
     */
    protected function getGuard(): GuardInterface
    {
        $guards = config('authentication.guards');
        if ($guards) {
            $guard = $guards[router()->getAccessPoint()] ?? null;

            if ($guard) {
                return new $guard;
            }
        }

        // default guard
        return new Junco\Usys\Guard\CookieGuard();
    }
}
