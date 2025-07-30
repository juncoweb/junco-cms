<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;
use Junco\Users\UserActivityToken;
use Junco\Users\UserHelper;

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
        UserHelper::verifyPassword($this->data['__password'], $curuser->password);

        if (!$this->data['fullname']) {
            return $this->unprocessable(_t('Please, fill in the name.'));
        }
        UserHelper::validateUsername($this->data['username']);

        // username
        if ($this->data['username'] != $curuser->username) {
            UserHelper::isUniqueUsername($this->data['username']);
        }

        // email
        if ($this->data['email'] && $this->data['email'] != $curuser->email) {
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
        $this->db->exec("UPDATE `#__users` SET ?? WHERE id = ?", $this->data, $curuser->id);

        // token
        if (isset($this->data['email'])) {
            UserActivityToken::generateAndSend('savemail', $curuser->id, $this->data['email'], $curuser->fullname);
        }
    }
}
