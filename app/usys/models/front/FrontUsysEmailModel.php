<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;
use Junco\Users\Enum\ActivityType;
use Junco\Users\UserActivityToken;
use Junco\Users\UserHelper;

class FrontUsysEmailModel extends Model
{
    /**
     * Get
     */
    public function getSaveData()
    {
        $input = $this->filter(GET, ['token' => 'text']);

        try {
            $data = [
                'error' => 0,
                'options' => config('usys.options')
            ];
            $token = UserActivityToken::from($input['token'], ActivityType::savemail);

            if (!$token) {
                throw new Exception(_t('The code used is invalid or has expired.'));
            }

            $token->destroy();
            $email = $token->getEmailTo();

            if (UserHelper::isUniqueEmail($email, 0, false)) {
                alert(422, _t('The email is being used on another account. Remember that if you have forgotten your password you can request a new.'));
            }

            // query
            db()->exec("UPDATE `#__users` SET email = ?, verified_email = 'yes' WHERE id = ?", $email, $token->getUserId());
        } catch (Exception $e) {
            $data['error'] = 1;
            $data['error_msg'] = $e->getMessage();
        }

        return $data;
    }
}
