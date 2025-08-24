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
        // data
        $this->filter(POST, [
            'fullname'   => 'text',
            'username'   => '',
            '__password' => '',
            'password'   => '',
            'email'      => 'email',
        ]);

        $curuser = curuser();

        //
        if (!$curuser->verifyPassword($this->data['__password'])) {
            return $this->unprocessable(_t('The current password is incorrect'));
        }

        if (!$this->data['fullname']) {
            return $this->unprocessable(_t('Please, fill in the name.'));
        }
        UserHelper::validateUsername($this->data['username']);

        // username
        if ($this->data['username'] != $curuser->getUsername()) {
            UserHelper::isUniqueUsername($this->data['username']);
        }

        // email
        if ($this->data['email'] && $this->data['email'] != $curuser->getEmail()) {
            UserHelper::isUniqueEmail($this->data['email']);
        } else {
            unset($this->data['email']);
        }

        // password
        if ($this->data['password'] && $this->data['password'] !== $this->data['__password']) {
            UserHelper::validatePassword($this->data['password']);

            $this->data['password'] = UserHelper::hash($this->data['password']);
        } else {
            unset($this->data['password']);
        }
        unset($this->data['__password']);

        // query
        $this->db->exec("UPDATE `#__users` SET ?? WHERE id = ?", $this->data, $curuser->getId());

        // token
        if (isset($this->data['email'])) {
            $token = UserActivityToken::generate(ActivityType::savemail, $curuser->getId(), $this->data['email']);

            (new UsysToken)->send($token, $curuser->getName());
        }
    }
}
