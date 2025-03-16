<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
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
	public function getPreLoginUserId(): int;

	/**
	 * PreLogin
	 * 
	 * @param int  $user_id
	 * @param bool $not_expire
	 * 
	 * @return bool
	 */
	public function preLogin(int $user_id = 0, bool $not_expire = false, ?array &$data = []): bool;

	/**
	 * PreLogin
	 * 
	 * @return bool
	 */
	public function takePreLogin(array &$data = []): bool;

	/**
	 * Login
	 * 
	 * @param int  $user_id
	 * @param bool $not_expire
	 * 
	 * @return bool
	 */
	public function login(int $user_id = 0, bool $not_expire = false, array &$data = []): bool;

	/**
	 * Logout
	 */
	public function logout(): bool;
}
