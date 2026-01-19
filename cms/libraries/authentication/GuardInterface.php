<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Authentication;

interface GuardInterface
{
    /**
     * Returns the user ID retrieved from storage.
     *
     * @return int
     */
    public function getUserId(): int;

    /**
     * Returns the user ID retrieved from storage.
     *
     * @return int
     */
    public function getDeferredUserId(): int;

    /**
     * Set deferred log in
     * 
     * @param int    $user_id
     * @param bool   $remember
     * @param ?array &$data
     * 
     * @return bool  Returns false if an error occurs; otherwise returns true.
     */
    public function setDeferredLogin(int $user_id = 0, bool $remember = false, ?array &$data = []): bool;

    /**
     * Execute a deferred login.
     * 
     * @param ?array &$data
     * 
     * @return bool  Returns false if an error occurs; otherwise returns true.
     */
    public function execDeferredLogin(array &$data = []): bool;

    /**
     * Login
     * 
     * @param int    $user_id       If it is zero, log out.
     * @param bool   $remember
     * @param ?array &$data
     * 
     * @return bool  Returns false if an error occurs; otherwise returns true.
     */
    public function login(int $user_id = 0, bool $remember = false, array &$data = []): bool;

    /**
     * Logout
     * 
     * @return bool  Returns false if an error occurs; otherwise returns true.
     */
    public function logout(): bool;
}
