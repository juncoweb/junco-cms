<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Users;

use Junco\Users\Enum\ActivityType;
use Junco\Users\Exception\UserActivityException;

class UserActivity
{
    // constant
    const OK               = 0;
    const USER_NOT_FOUND   = -1;
    const USER_NOT_ACTIVE  = -2;
    const INVALID_PASSWORD = -7;
    //
    const INVALID_TOKEN    = -20;

    // vars
    protected $db;
    protected string $user_ip;
    protected int    $locks_level;
    //
    protected int    $expires_at = 0;
    protected int    $lock_id    = 0;
    protected int    $counter    = -1;

    /**
     * Constructor
     */
    public function __construct(protected ActivityType $type)
    {
        $this->db = db();
        $this->user_ip = curuser()->getIpAsBinary();
        $this->locks_level = config('users.locks_level');
    }

    /**
     * Verify the existence of IP blocking.
     * 
     * @throws UserActivityException
     *
     * @return void
     */
    public function verify(): void
    {
        if (!$this->locks_level) {
            return;
        }

        // query
        $data = $this->db->query("
		SELECT 
		 id ,
		 lock_counter ,
		 expires_at ,
		 (expires_at > NOW()) AS expired
		FROM `#__users_activities_locks`
		WHERE user_ip = ?
		AND lock_type = ?", $this->user_ip, $this->type)->fetch();

        if ($data) {
            if ($data['expired']) {
                $this->expires_at = strtotime($data['expires_at']);
                throw new UserActivityException(_t('The IP is locked'));
            }

            $this->lock_id = $data['id'];
            $this->counter = $data['lock_counter'];
        }
    }

    /**
     * Record
     *
     * @param int   $code      The activity code.
     * @param int   $user_id
     * @param mixed $context
     * 
     * @return void
     */
    public function record(int $code, int $user_id = 0, array $context = []): void
    {
        $context = $context
            ? json_encode($context)
            : '';

        $this->db->exec("
        INSERT INTO `#__users_activities` (
         user_id,
         user_ip,
         activity_type,
         activity_code,
         activity_context
        ) VALUES (?, ?, ?, ?, ?)", $user_id, $this->user_ip, $this->type, $code, $context);

        if ($this->locks_level) {
            if ($code < 0) {
                $this->lock($this->locks_level);
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
    protected function lock(int $locks_level): void
    {
        $lifetime = $this->getLifetime($locks_level, $this->counter + 1);

        if ($this->lock_id) {
            $this->db->exec("
			UPDATE `#__users_activities_locks`
			SET
			 lock_counter = lock_counter + 1,
			 expires_at = NOW() + INTERVAL $lifetime SECOND
			WHERE id = ?", $this->lock_id);
        } else {
            $this->db->exec("
			INSERT INTO `#__users_activities_locks` (user_ip, lock_type, expires_at)
			VALUES (?, ?, NOW() + INTERVAL $lifetime SECOND)", $this->user_ip, $this->type);
            $this->lock_id = $this->db->lastInsertId();
        }

        $expires_at = $this->db->query("
        SELECT expires_at
        FROM `#__users_activities_locks`
        WHERE id = ?", $this->lock_id)->fetchColumn();

        $this->expires_at = strtotime($expires_at);
    }

    /**
     * Unlock
     */
    protected function unlock(): void
    {
        $this->db->exec("DELETE FROM `#__users_activities_locks` WHERE id = ?", $this->lock_id);
    }

    /**
     * Get
     */
    protected function getLifetime(int $locks_level, int $exp): int
    {
        switch ($locks_level) {
            default:
            case 1:
                $base   = 3;
                $factor = 5;
                break;
            case 2:
                $base   = 3;
                $factor = 7;
                break;
            case 3:
                $base   = 3;
                $factor = 9;
                break;
        }

        return ($base ** $exp) * $factor;
    }

    /**
     * Gets a text from the code.
     * 
     * @param int    $code
     * 
     * @return string
     */
    public static function getMessage(int $code): string
    {
        switch ($code) {
            case  self::OK:
                return _t('The action has been completed correctly.');

            case self::USER_NOT_FOUND:
                return _t('The user has not been found.');

            case self::USER_NOT_ACTIVE:
                return _t('The user is not active.');

            case self::INVALID_PASSWORD:
                return _t('The user has entered an invalid password.');

            case self::INVALID_TOKEN:
                return _t('An invalid token has been entered.');
        }

        return 'Unknown.';
    }
}
