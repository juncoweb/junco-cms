<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

namespace Junco\Users\Token;

use Junco\Users\Enum\ActivityType;
use Junco\Users\UserActivity;
use Junco\Users\UserActivityToken;

class TokenValidator
{
    /**
     * Returns an instance of the UserActivityToken class or false on error
     *
     * @param string       $token
     * @param ActivityType $type
     * @param bool         $expires
     *
     * @return UserActivityToken|false
     */
    public function from(string $token, ActivityType $type, bool $expires = true): UserActivityToken|false
    {
        $manager = new Token($token);

        if (!$manager->verify()) {
            return $this->error($type, -1, $token);
        }

        $selector = $manager->getSelector();
        $lifetime = ($type === ActivityType::activation) // $expires // 
            ? 0
            : config('users-activities.token_lifetime');

        $data = $this->getTokenData($selector, $lifetime);

        if (!$data) {
            return $this->error($type, -10, $token);
        }

        if (!$type->isEqual($data['activity_type'])) {
            return $this->error($type, -11, $token, $data['user_id'], $data['id']);
        }

        if ($data['status']) {
            return $this->error($type, -12, $token, $data['user_id'], $data['id']);
        }

        if ($data['expired']) {
            return $this->error($type, -13, $token, $data['user_id'], $data['id']);
        }

        if (!$manager->validate($data['token_validator'])) {
            return $this->error($type, -14, $token, $data['user_id'], $data['id']);
        }

        return new UserActivityToken($token, $type, $data['id'], $data['user_id'], $data['token_to']);
    }

    /**
     * Get
     *
     * @param string $selector
     * @param int    $lifetime
     *
     * @return array|false
     */
    protected function getTokenData(string $selector, int $lifetime = 0): array|false
    {
        $expired  = ($lifetime > 0)
            ? "NOW() > DATE_ADD(a.created_at, INTERVAL $lifetime HOUR)"
            : "FALSE";

        return db()->query("
        SELECT
         a.id ,
         a.user_id ,
         a.activity_type ,
         ($expired) AS expired ,
         t.token_validator ,
         t.token_to ,
         t.status
        FROM `#__users_activities_tokens` t
        LEFT JOIN `#__users_activities` a ON ( a.id = t.activity_id )
        WHERE t.token_selector = ?", $selector)->fetch();
    }

    /**
     * Error
     *
     * @return false
     */
    protected function error(ActivityType $type, int $code, string $token, int $user_id = 0, int $activity_id = 0): false
    {
        (new UserActivity($type))->record(UserActivity::INVALID_TOKEN, $user_id, [
            'activity_id' => $activity_id,
            'code'        => $code,
            'token'       => $token
        ]);

        return false;
    }
}
