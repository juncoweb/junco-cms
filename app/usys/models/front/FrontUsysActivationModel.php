<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;
use Junco\Users\Enum\UserStatus;
use Junco\Users\UserActivityToken;

class FrontUsysActivationModel extends Model
{
    /**
     * Get
     */
    public function getIndexData()
    {
        // vars
        $data = [
            'error' => 0,
            'options' => config('usys.options')
        ];

        try {
            $token = UserActivityToken::get(GET, 'activation')->destroy();

            // query
            db()->safeExec("UPDATE `#__users` SET verified_email = 'yes', status = ? WHERE id = ?", UserStatus::active, $token->user_id);

            // set
            curuser()->login($token->user_id);
        } catch (Exception $e) {
            $data['error'] = 1;
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
