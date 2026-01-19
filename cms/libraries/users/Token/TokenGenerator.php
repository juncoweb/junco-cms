<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Users\Token;

use Junco\Users\Enum\ActivityType;
use Junco\Users\UserActivityToken;

class TokenGenerator
{
    // vars
    protected $db;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = db();
    }

    /**
     * Generate a token
     *
     * @param ActivityType $type      One type of token.
     * @param int          $user_id   The user ID.
     * @param string       $email_to  The user's email where the token will be sent.
     * 
     * @return UserActivityToken
     */
    public function generate(ActivityType $type, int $user_id, string $email_to): UserActivityToken
    {
        $this->destroyAll($type, $user_id);
        return $this->newToken($type, $user_id, $email_to);
    }

    /**
     * Destroy all tokens
     *
     * @param ActivityType $type
     * @param int          $user_id
     * 
     * @return int
     */
    protected function destroyAll(ActivityType $type, int $user_id): int
    {
        return $this->db->exec("
		UPDATE `#__users_activities_tokens`
		SET status = -1
		WHERE status = 0
		AND activity_id IN (
            SELECT id
            FROM `#__users_activities`
            WHERE user_id = ?
            AND activity_type = ?
        )", $user_id, $type);
    }

    /**
     * Generate a token
     *
     * @param ActivityType $type      One type of token.
     * @param int          $user_id   The user ID.
     * @param string       $email_to  The user's email where the token will be sent.
     * 
     * @return UserActivityToken
     */
    protected function newToken(ActivityType $type, int $user_id, string $email_to): UserActivityToken
    {
        $user_ip = curuser()->getIpAsBinary();

        // query - I record activity
        $this->db->exec("INSERT INTO `#__users_activities` (user_id, user_ip, activity_type) VALUES (?, ?, ?)", $user_id, $user_ip, $type);

        $manager     = new Token;
        $token       = $manager->generate();
        $selector    = $manager->getSelector();
        $validator   = $manager->getValidator();
        $activity_id = $this->db->lastInsertId();

        // query - save the token
        $this->db->exec("
        INSERT INTO `#__users_activities_tokens` (
         activity_id,
         token_selector,
         token_validator,
         token_to
        ) VALUES (?, ?, ?, ?)", $activity_id, $selector, $validator, $email_to);

        return new UserActivityToken($token, $type, $activity_id, $user_id, $email_to);
    }
}
