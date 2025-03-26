<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;
use Junco\Users\UserActivityToken;
use Junco\Users\UserHelper;

class FrontUsysEmailModel extends Model
{
    /**
     * Get
     */
    public function getSaveData()
    {
        // vars
        $data = [
            'error' => 0,
            'options' => config('usys.options')
        ];

        try {
            $token = UserActivityToken::get(GET, 'savemail')->destroy();

            if (UserHelper::isUniqueEmail($token->to, 0, false)) {
                throw new Exception(_t('The email is being used on another account. Remember that if you have forgotten your password you can request a new.'));
            }

            // query
            db()->safeExec("UPDATE `#__users` SET email = ?, verified_email = 'yes' WHERE id = ?", $token->to, $token->user_id);
        } catch (Exception $e) {
            $data['error'] = 1;
            $data['error_msg'] = $e->getMessage();
        }

        return $data;
    }
}
