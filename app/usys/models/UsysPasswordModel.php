<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;
use Junco\Users\Enum\ActivityType;
use Junco\Users\Enum\UserStatus;
use Junco\Users\UserActivityToken;
use Junco\Users\UserHelper;
use Junco\Usys\UsysToken;

class UsysPasswordModel extends Model
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
     * Send token
     */
    public function sentToken()
    {
        $data = $this->filter(POST, ['email_username' => '']);

        $user = UserHelper::getUserFromInput($data['email_username']);

        if (!$user) {
            return $this->unprocessable(_t('Invalid email/username.'));
        }

        if (!$user->isActive()) {
            return $this->result()->redirectTo(url('/usys/message', ['op' => 'login']));
        }

        // token
        $token = UserActivityToken::generate(ActivityType::savepwd, $user->getId(), $user->getEmail());
        $result = (new UsysToken)->send($token, $user->getName());

        if (!$result) {
            return $this->unprocessable(_t('An error has occurred in the mail server. Please, try again later.'));
        }

        return $this->result()->redirectTo(url('/usys/message', ['op' => 'reset-pwd']));
    }

    /**
     * Update
     */
    public function update()
    {
        $data = $this->filter(POST, [
            'token'    => 'text',
            'password' => 'required',
            'verified' => 'required',
        ]);

        // vars
        $token = UserActivityToken::from($data['token'], ActivityType::savepwd);

        if (!$token) {
            return $this->unprocessable(_t('The code used is invalid or has expired.'));
        }

        $user_id = $token->getUserId();

        if ($data['password'] !== $data['verified']) {
            return $this->unprocessable(_t('Passwords do not match.'));
        }

        UserHelper::validatePassword($data['password']);
        $data['password'] = UserHelper::hash($data['password']);

        // query
        $this->db->exec("UPDATE `#__users` SET password = ? WHERE id = ?", $data['password'], $user_id);

        $token->destroy();
        auth()->login($user_id);

        return $this->result()->redirectTo(url('/usys/message', ['op' => 'savepwd']));
    }
}
