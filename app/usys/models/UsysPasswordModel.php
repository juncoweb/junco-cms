<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;
use Junco\Users\Enum\UserStatus;
use Junco\Users\UserActivityToken;
use Junco\Users\UserHelper;

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
        // data
        $this->filter(POST, ['email_username' => '']);

        $user = UserHelper::getUserFromInput($this->data['email_username']);

        if (!$user) {
            return $this->unprocessable(_t('Invalid email/username.'));
        }

        if (!UserStatus::active->isEqual($user['status'])) {
            return $this->result()->redirectTo(url('/usys/message', ['op' => 'login']));
        }

        // token
        $result = UserActivityToken::generateAndSend('savepwd', $user['id'], $user['email'], $user['fullname']);

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
        // data
        $this->filter(POST, [
            'password' => 'required',
            'verified' => 'required',
        ]);

        // vars
        $token = UserActivityToken::get(POST, 'savepwd');

        if ($this->data['password'] !== $this->data['verified']) {
            return $this->unprocessable(_t('Passwords do not match.'));
        }

        UserHelper::validatePassword($this->data['password']);
        $this->data['password'] = UserHelper::hash($this->data['password']);

        // query
        $this->db->exec("UPDATE `#__users` SET password = ? WHERE id = ?", $this->data['password'], $token->user_id);

        $token->destroy();
        curuser()->login($token->user_id);

        return $this->result()->redirectTo(url('/usys/message', ['op' => 'savepwd']));
    }
}
