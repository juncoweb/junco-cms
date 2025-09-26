<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;
use Junco\Users\Enum\ActivityType;
use Junco\Users\Enum\UserStatus;
use Junco\Users\UserActivityToken;

class FrontUsysActivationModel extends Model
{
    /**
     * Get
     */
    public function getIndexData()
    {
        $input = $this->filter(GET, ['token' => 'text']);

        try {
            $data = [
                'error' => 0,
                'options' => config('usys.options')
            ];

            $token = UserActivityToken::from($input['token'], ActivityType::activation);

            if (!$token) {
                throw new Exception(_t('The code used is invalid or has expired.'));
            }

            $token->destroy();
            $user_id = $token->getUserId();

            // query
            db()->exec("UPDATE `#__users` SET verified_email = 'yes', status = ? WHERE id = ?", UserStatus::active, $user_id);

            // set
            auth()->login($user_id);
        } catch (Exception $e) {
            $data['error']     = 1;
            $data['error_msg'] = $e->getMessage();
        }

        return $data;
    }

    /**
     * Get
     */
    public function getResetData()
    {
        return [
            'options' => config('usys.options'),
        ];
    }
}
