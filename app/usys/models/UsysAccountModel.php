<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;
use Junco\Users\Enum\ActivityType;
use Junco\Users\UserActivityToken;
use Junco\Users\UserHelper;
use Junco\Usys\UsysToken;

class UsysAccountModel extends Model
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
     * Update
     */
    public function update()
    {
        $data = $this->filter(POST, [
            'user_name'   => 'text',
            'user_username'   => '',
            '__user_password' => '',
            'user_password'   => '',
            'user_email'      => 'email',
        ]);

        $curuser = curuser();

        //
        if (!$curuser->verifyPassword($data['__user_password'])) {
            return $this->unprocessable(_t('The current password is incorrect'));
        }

        if (!$data['user_name']) {
            return $this->unprocessable(_t('Please, fill in the name.'));
        }
        UserHelper::validateUsername($data['user_username']);

        // username
        if ($data['user_username'] != $curuser->getUsername()) {
            UserHelper::isUniqueUsername($data['user_username']);
        }

        // email
        if ($data['user_email'] && $data['user_email'] != $curuser->getEmail()) {
            UserHelper::isUniqueEmail($data['user_email']);
        } else {
            unset($data['user_email']);
        }

        // password
        if ($data['user_password'] && $data['user_password'] !== $data['__user_password']) {
            UserHelper::validatePassword($data['user_password']);

            $data['user_password'] = UserHelper::hash($data['user_password']);
        } else {
            unset($data['user_password']);
        }
        unset($data['__user_password']);

        // query
        $this->db->exec("UPDATE `#__users` SET ?? WHERE id = ?", $data, $curuser->getId());

        // token
        if (isset($data['user_email'])) {
            $token = UserActivityToken::generate(ActivityType::savemail, $curuser->getId(), $data['user_email']);

            (new UsysToken)->send($token, $curuser->getName());
        }
    }
}
