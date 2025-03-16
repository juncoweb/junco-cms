<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Users;

use Junco\Users\Exception\UserActivityException;

class UserActivity
{
	// vars
	protected $db;
	protected string $user_ip;
	//
	protected int    $expires_at	= 0;
	protected int    $lock_id		= 0;
	protected int    $counter		= -1;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->db = db();
		$this->user_ip = curuser()->getIpAsBinary();
	}

	/**
	 * Verify the existence of IP blocking.
	 *
	 * @param string $type  The lock type.
	 */
	public function verify($type)
	{
		// query
		$data = $this->db->safeFind("
		SELECT 
		 id ,
		 lock_counter ,
		 expires_at ,
		 (expires_at > NOW()) AS expire
		FROM `#__users_activities_locks`
		WHERE user_ip = ?
		AND lock_type = ?", $this->user_ip, $type)->fetch();

		if ($data) {
			if ($data['expire']) {
				$this->expires_at = strtotime($data['expires_at']);
				throw new UserActivityException(_t('The IP is locked'));
			}

			$this->lock_id = $data['id'];
			$this->counter = $data['lock_counter'];
		}
	}

	/**
	 * Create an instance of the class
	 *
	 * @param string $type     The activity type.
	 * @param int    $code     The activity code.
	 * @param int    $user_id
	 * @param mixed  $data
	 */
	public function record(string $type, int $code, int $user_id = 0, array $context = [])
	{

		$this->db->safeExec("INSERT INTO `#__users_activities` (??) VALUES (??)", [
			'user_id'			=> $user_id,
			'user_ip'			=> $this->user_ip,
			'activity_type'		=> $type,
			'activity_code'		=> $code,
			'activity_context'	=> $context ? json_encode($context) : ''
		]);

		$locks_level = config('users.locks_level');

		if ($locks_level) {
			if ($code < 0) {
				$this->lock($type, $locks_level);
			} elseif (!$code && $this->lock_id) {
				$this->unlock();
			}
		}
	}

	/**
	 * Get
	 * 
	 * @param array $data
	 * 
	 * @return array
	 */
	public function getExpiresData(array $data = []): array
	{
		$data['lockExpires'] = $this->expires_at;

		return $data;
	}

	/**
	 * Lock
	 */
	protected function lock(string $type, int $locks_level): void
	{
		$lifetime = $this->getLifetime($locks_level, $this->counter + 1);

		if ($this->lock_id) {
			$this->db->safeExec("
			UPDATE `#__users_activities_locks`
			SET
			 lock_counter = lock_counter + 1,
			 expires_at = NOW() + INTERVAL $lifetime SECOND
			WHERE id = ?", $this->lock_id);
		} else {
			$this->db->safeExec("
			INSERT INTO `#__users_activities_locks` (user_ip, lock_type, expires_at)
			VALUES (?, ?, NOW() + INTERVAL $lifetime SECOND)", $this->user_ip, $type);
			$this->lock_id = $this->db->lastInsertId();
		}

		$this->expires_at = strtotime(
			$this->db->safeFind("SELECT expires_at FROM `#__users_activities_locks` WHERE id = ?", $this->lock_id)->fetchColumn()
		);
	}

	/**
	 * Unlock
	 */
	protected function unlock(): void
	{
		$this->db->safeExec("DELETE FROM `#__users_activities_locks` WHERE id = ?", $this->lock_id);
	}

	/**
	 * Get
	 */
	protected function getLifetime(int $locks_level, int $exp): int
	{
		switch ($locks_level) {
			default:
			case 1:
				$base = 3;
				$factor = 5;
				break;
			case 2:
				$base = 3;
				$factor = 7;
				break;
			case 3:
				$base = 3;
				$factor = 9;
				break;
		}

		return ($base ** $exp) * $factor;
	}
}
