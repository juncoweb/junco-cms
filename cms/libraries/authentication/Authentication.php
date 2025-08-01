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
     * Returns the current user ID or zero
     *
     * @param string $token
     */
    public function getCurrentUserId(): int
    {
        return $this->guard->getUserId();
    }

    /**
     * Returns the current user ID or zero
     *
     * @param string $token
     */
    public function getPreLoginUserId(): int
    {
        return $this->guard->getPreLoginUserId();
    }

    /**
     * PreLogin
     * 
     * @param int   $user_id
     * @param bool  $not_expire
     * @param array $data
     * 
     * @return bool
     */
    public function preLogin(int $user_id = 0, bool $not_expire = false, ?array &$data = null): bool
    {
        $data ??= [];
        return $this->guard->preLogin($user_id, $not_expire, $data);
    }

    /**
     * Login
     * 
     * @param array $data
     * 
     * @return bool
     */
    public function takePreLogin(?array &$data = null): bool
    {
        $data ??= [];
        return $this->guard->takePreLogin($data);
    }

    /**
     * Login
     * 
     * @param int   $user_id
     * @param bool  $not_expire
     * @param array $data
     * 
     * @return bool
     */
    public function login(int $user_id = 0, bool $not_expire = false, ?array &$data = null): bool
    {
        $data ??= [];
        return $this->guard->login($user_id, $not_expire, $data);
    }

    /**
     * Logout
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
