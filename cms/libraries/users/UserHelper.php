<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Users;

use Junco\Users\Exception\UserValidationException;

class UserHelper
{
	/**
	 * Validate username
	 * 
	 * @param string $value
	 * @param bool   $throw
	 * 
	 * @return bool
	 * 
	 * @throws UserValidationException
	 */
	public static function validateUsername(string $value, bool $throw = true): bool
	{
		if (!$value) {
			if ($throw) {
				throw new UserValidationException(_t('Please, fill in the username.'));
			}
			return false;
		}

		if (!preg_match('/^[\w]{6,24}$/iu', $value)) {
			if ($throw) {
				throw new UserValidationException(_t('Please, choose a username with 6 to 24 characters, using only letters and numbers.'));
			}
			return false;
		}

		return true;
	}

	/**
	 * Validate email
	 * 
	 * @param string $value
	 * @param bool   $throw
	 * 
	 * @return bool
	 * 
	 * @throws Exception
	 */
	public static function validateEmail(string $value, bool $throw = true): bool
	{
		if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
			if ($throw) {
				throw new UserValidationException(_t('Your email does not pass the validity check.') . ' ' . _t('Please check it and if the problem persists, contact the administration.'));
			}

			return false;
		}

		return true;
	}

	/**
	 * Verify slug
	 * 
	 * @param string $value
	 * @param bool   $throw
	 * 
	 * @return bool
	 * 
	 * @throws Exception
	 */
	public static function verifySlug(string $value, bool $throw = true): bool
	{
		if (!preg_match('/^[a-z0-9]{6,24}$/', $value)) {
			if ($throw) {
				throw new UserValidationException(_t('The short URL is incorrect. Use only alphanumeric characters.'));
			}
			return false;
		}

		return true;
	}

	/**
	 * Validate password
	 * 
	 * @param string $value
	 * @param bool   $throw
	 * 
	 * @return bool
	 * 
	 * @throws Exception
	 */
	public static function validatePassword(string $value, bool $throw = true): bool
	{
		$config = config('users');
		$len = strlen($value);

		if ($len > $config['users.password_max_length']) {
			if ($throw) {
				throw new UserValidationException(sprintf(_t('The password is too long. The maximum is %d characters.'), $config['users.password_max_length']));
			}
			return false;
		}

		if ($len < $config['users.password_min_length']) {
			if ($throw) {
				throw new UserValidationException(sprintf(_t('The password is too short. The minimum is %d characters.'), $config['users.password_min_length']));
			}
			return false;
		}

		if ($config['users.password_level'] > 0 && !preg_match('@[0-9]@', $value)) {
			if ($throw) {
				throw new UserValidationException(_t('The password requires at least one number.'));
			}
			return false;
		}

		if ($config['users.password_level'] > 1 && !preg_match('@[A-Z]@', $value)) {
			if ($throw) {
				throw new UserValidationException(_t('The password requires at least one uppercase letter.'));
			}
			return false;
		}

		if ($config['users.password_level'] > 2 && !preg_match('@[\W]@', $value)) {
			if ($throw) {
				throw new UserValidationException(_t('The password requires at least one symbol.'));
			}
			return false;
		}

		return true;
	}

	/**
	 * Is unique username.
	 * 
	 * @param string $username
	 * @param int    $user_id
	 * @param bool   $throw
	 * 
	 * @return bool
	 * 
	 * @throws Exception
	 */
	public static function isUniqueUsername(string $username, int $user_id = 0, bool $throw = true): bool
	{
		// query
		$current_id = db()->safeFind("SELECT id FROM `#__users` WHERE username = ?", $username)->fetchColumn();

		if ($current_id && $current_id != $user_id) {
			if ($throw) {
				throw new UserValidationException(_t('The username is being used.'));
			}
			return false;
		}

		return true;
	}

	/**
	 * Is unique email.
	 * 
	 * @param string $email
	 * @param int    $user_id
	 * @param bool   $throw
	 * 
	 * @return bool
	 * 
	 * @throws Exception
	 */
	public static function isUniqueEmail(string $email, int $user_id = 0, bool $throw = true): bool
	{
		// query
		$current_id = db()->safeFind("SELECT id FROM `#__users` WHERE email = ?", $email)->fetchColumn();

		if ($current_id && $current_id != $user_id) {
			if ($throw) {
				throw new UserValidationException(_t('The email is being used on another account. Remember that if you have forgotten your password you can request a new.'));
			}
			return false;
		}

		return true;
	}

	/**
	 *
	 * @param string $password
	 * 
	 * @return string
	 */
	public static function hash(string $password): string
	{
		return password_hash($password, PASSWORD_DEFAULT);
	}

	/**
	 * Check if you need to rehash.
	 * 
	 * @param string $password
	 * 
	 * @return string
	 */
	public static function rehash(string $password, int $user_id)
	{
		if (password_needs_rehash($password, PASSWORD_DEFAULT)) {
			db()->safeExec("UPDATE `#__users` SET password = ? WHERE id = ?", self::hash($password), $user_id);
		}
	}

	/**
	 *	Verify password
	 *
	 * @param string $password
	 * @param string $current
	 * @param bool   $throw

	 * @return bool
	 * 
	 * @throws Exception
	 */
	public static function verifyPassword(string $password, string $current, bool $throw = true): bool
	{
		if (!password_verify($password, $current)) {
			if ($throw) {
				throw new UserValidationException(_t('The current password is incorrect'));
			}
			return false;
		}

		return true;
	}

	/**
	 * Get
	 * 
	 * @param string $input
	 * 
	 * @return ?array
	 */
	public static function getUserFromInput(string $input): ?array
	{
		$db = db();

		if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
			$db->where("email = ?", $input);
		} elseif (UserHelper::validateUsername($input, false)) {
			$db->where("username = ?", $input);
		} else {
			return null;
		}

		return $db->safeFind("
		SELECT
		 id ,
		 fullname ,
         email ,
		 password ,
		 status
		FROM `#__users`
		[WHERE]")->fetch() ?: null;
	}
}
