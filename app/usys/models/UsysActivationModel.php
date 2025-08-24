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

class UsysActivationModel extends Model
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
     * Send
     */
    public function sendToken()
    {
        // data
        $this->filter(POST, [
            'option'         => '',
            'email_username' => '',
            'new_email'      => ''
        ]);

        /**
         * Instance 1
         */
        $user = UserHelper::getUserFromInput($this->data['email_username']);

        if (!$user) {
            return $this->unprocessable(_t('Invalid email/username.'));
        }

        if ($user->isActive()) {
            return $this->unprocessable(_t('Your account is active. Please, enter from the login.'));
        }

        if ($this->data['option'] == 1) {
            return $this->unprocessable($this->obfuscateEmail($user->getEmail()), 5);
        }

        /**
         * Instance 2
         */
        $user_id     = $user->getId();
        $email       = $user->getEmail();
        $is_inactive = $user->getStatus() === UserStatus::inactive;

        // update email
        if (
            $is_inactive
            && $this->data['new_email']
            && $this->data['new_email'] !== $email
        ) {
            $email = $this->updateEmail($this->data['new_email'], $user_id);
        }

        // token
        $type = $is_inactive
            ? ActivityType::activation
            : ActivityType::signup;

        $token = UserActivityToken::generate($type, $user_id, $email);
        $result = (new UsysToken)->send($token, $user->getName());

        if (!$result) {
            return $this->unprocessable(_t('An error has occurred in the mail server. Please, try again later.'));
        }

        return $this->result()->redirectTo(url('/usys/message', ['op' => 'reset-act']));
    }

    /**
     * Ofuscate
     * 
     * @param string $email
     * @param int    $n			Number of visible characters.
     * 
     * @return string
     */
    protected function obfuscateEmail(string $email, int $n = 1): string
    {
        $partial = explode('@', $email, 2);

        return substr($partial[0], 0, $n) . str_repeat('*', strlen($partial[0]) - $n) . '@' . $partial[1];
    }

    /**
     * Update
     * 
     * @throws Exception
     * 
     * @return string
     */
    protected function updateEmail(string $email, int $user_id): string
    {
        UserHelper::validateEmail($email);
        UserHelper::isUniqueEmail($email, $user_id);

        // query
        $this->db->exec("UPDATE `#__users` SET email = ? WHERE id = ?", $email, $user_id);

        return $email;
    }
}
