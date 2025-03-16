<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Session\Handler;

class RedisHandler implements \SessionHandlerInterface
{
	// vars
	protected $redis		= null;
	protected $prefix		= '';
	protected $maxlifetime	= 0;

	/**
	 * Initialize session
	 *
	 * @param    string $save_path		The path where to store/retrieve the session.
	 * @param    string $session_name	The session name.
	 *
	 * @return	 bool					The return value (usually true on success, false on failure).
	 */
	public function open(string $save_path, string $session_name): bool
	{
		try {
			$this->redis = new Redis();
			$this->redis->pconnect(SESSION_REDIS_HOSTNAME, SESSION_REDIS_PORT);
			$this->prefix = 'sess.';
			$this->maxlifetime = ini_get('session.gc_maxlifetime');
		} catch (RedisException $e) {
			//
		}
		return true;
	}

	/**
	 * Close the session
	 *
	 * @return	 bool	The return value (usually true on success, false on failure).
	 */
	public function close(): bool
	{
		return true;
	}

	/**
	 * Read session data
	 *
	 * @param    string  $session_id	The session id.
	 *
	 * @return	 string					Returns an encoded string of the read data. If nothing was read, it must return false.
	 */
	public function read(string $session_id): string|false
	{
		return $this->redis->get($this->prefix . $session_id) ?: '';
	}

	/**
	 * Write session data
	 *
	 * @param    string  $session_id	The session id.
	 * @param    string  $data			The encoded session data.
	 *
	 * @return	 bool					The return value (usually true on success, false on failure).
	 */
	public function write(string $session_id, string $data): bool
	{
		if ($session_id) {
			$this->redis->set($this->prefix . $session_id, $data, $this->maxlifetime);
		}

		return true;
	}

	/**
	 * Destroy a session
	 *
	 * @param    string  $session_id	The session ID being destroyed.
	 *
	 * @return	 bool					The return value (usually true on success, false on failure).
	 */
	public function destroy(string $session_id): bool
	{
		$this->redis->unlink($this->prefix . $session_id);

		return true;
	}

	/**
	 * Cleanup old sessions
	 *
	 * @param    $max_lifetime	Sessions that have not updated for the last max_lifetime seconds will be removed.
	 * 
	 * @return	 int|false		Returns the number of deleted sessions on success, or false on failure.
	 */
	public function gc(int $maxlifetime): int|false
	{
		return 1;
	}
}
