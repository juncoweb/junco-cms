<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
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
            'fullname'   => 'text',
            'username'   => '',
            '__password' => '',
            'password'   => '',
            'email'      => 'email',
        ]);

        $curuser = curuser();

        //
        if (!$curuser->verifyPassword($data['__password'])) {
            return $this->unprocessable(_t('The current password is incorrect'));
        }

        if (!$data['fullname']) {
            return $this->unprocessable(_t('Please, fill in the name.'));
        }
        UserHelper::validateUsername($data['username']);

        // username
        if ($data['username'] != $curuser->getUsername()) {
            UserHelper::isUniqueUsername($data['username']);
        }

        // email
        if ($data['email'] && $data['email'] != $curuser->getEmail()) {
            UserHelper::isUniqueEmail($data['email']);
        } else {
            unset($data['email']);
        }

        // password
        if ($data['password'] && $data['password'] !== $data['__password']) {
            UserHelper::validatePassword($data['password']);

            $data['password'] = UserHelper::hash($data['password']);
        } else {
            unset($data['password']);
        }
        unset($data['__password']);

        // query
        $this->db->exec("UPDATE `#__users` SET ?? WHERE id = ?", $data, $curuser->getId());

        // token
        if (isset($data['email'])) {
            $token = UserActivityToken::generate(ActivityType::savemail, $curuser->getId(), $data['email']);

            (new UsysToken)->send($token, $curuser->getName());
        }
    }
}
