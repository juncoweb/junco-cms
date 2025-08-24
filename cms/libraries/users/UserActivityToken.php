<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Users;

use Junco\Users\Enum\ActivityType;
use Junco\Users\Token\TokenGenerator;
use Junco\Users\Token\TokenValidator;

class UserActivityToken
{
    /**
     * Constructor
     * 
     * @param string       $token,
     * @param ActivityType $type,
     * @param int          $activity_id,
     * @param int          $user_id,
     * @param string       $email_to
     */
    public function __construct(
        protected string       $token,
        protected ActivityType $type,
        protected int          $activity_id,
        protected int          $user_id,
        protected string       $email_to
    ) {}

    /**
     * Destroy the current token
     *
     * @return self
     */
    public function destroy(): self
    {
        db()->exec("UPDATE `#__users_activities_tokens` SET status = 1 WHERE activity_id = ?", $this->activity_id);
        return $this;
    }

    /**
     * Get
     *
     * @return ActivityType
     */
    public function getType(): ActivityType
    {
        return $this->type;
    }

    /**
     * Get
     *
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * Get
     *
     * @return string
     */
    public function getEmailTo(): string
    {
        return $this->email_to;
    }

    /**
     * To string
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->token;
    }

    /**
     * Create an instance of the class
     *
     * @param string       $token
     * @param ActivityType $type
     * @param bool         $expires
     *
     * @return self|false
     */
    public static function from(string $token, ActivityType $type, bool $expires = true): self|false
    {
        return (new TokenValidator)->from($token, $type, $expires);
    }

    /**
     * Generate a token
     *
     * @param ActivityType $type
     * @param int          $user_id
     * @param string       $email_to  The user's email where the token will be sent.
     * 
     * @return self
     */
    public static function generate(ActivityType $type, int $user_id, string $email_to): self
    {
        return (new TokenGenerator)->generate($type, $user_id, $email_to);
    }
}
