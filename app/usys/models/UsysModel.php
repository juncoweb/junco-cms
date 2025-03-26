<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;
use Junco\Users\Exception\UserActivityException;
use Junco\Users\Exception\UserNotActiveException;
use Junco\Users\Exception\UserNotFoundException;
use Junco\Users\Exception\UserValidationException;
use Junco\Users\Tractor\LoginTractor;
use Junco\Users\Tractor\SignupTractor;
use Junco\Users\UserActivityToken;

class UsysModel extends Model
{
    // vars
    protected $db = null;
    //
    protected $legal    = null;
    protected $verified    = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->db = db();
    }

    /**
     * Signup
     */
    public function signup()
    {
        // data
        $this->filter(POST, [
            'fullname'    => '',
            'username'    => 'text',
            'email'        => 'email',
            'password'    => 'required',
            'verified'    => '',
            'legal'        => 'bool',
        ]);

        //
        try {
            $token   = UserActivityToken::get(POST, 'signup', true);
            $user_id = $token ? $token->user_id : 0;
            $tractor = new SignupTractor();
            $result  = $tractor->signup(
                $this->data['fullname'],
                $this->data['username'],
                $this->data['email'],
                $this->data['password'],
                $this->data['verified'],
                $this->data['legal'],
                $user_id
            );
        } catch (UserValidationException $e) {
            return Xjs::response($e->getMessage(), $e->getCode());
        } catch (Throwable $e) {
            app('logger')->critical($e->getMessage());
            throw $e;
        }

        if ($result) {
            $token->destroy();
            curuser()->login($user_id);
            return Xjs::reloadPage();
        }

        return Xjs::redirectTo(url('/usys/message', ['op' => 'signup']));
    }

    /**
     * Login
     */
    public function login()
    {
        // data
        $this->filter(POST, [
            'email_username'    => 'text',
            'password'            => '',
            'not_expire'        => 'bool',
            'redirect'            => ''
        ]);

        try {
            $tractor = new LoginTractor;
            $tractor->validateCredencial(
                $this->data['email_username'],
                $this->data['password'] ?? ''
            );

            $mfa_url = config('usys.mfa_url');

            if ($mfa_url) {
                $tractor->preLogin($this->data['not_expire']);
                $url = UsysHelper::getUrl($mfa_url, $this->data['redirect']);

                return Xjs::redirectTo($url);
            }

            $tractor->login($this->data['not_expire']);

            if ($this->data['redirect'] == -1) {
                return Xjs::goBack();
            }

            if ($this->data['redirect']) {
                return Xjs::redirectTo($this->data['redirect']);
            }

            return Xjs::reloadPage();
        } catch (UserNotActiveException $e) {
            return Xjs::redirectTo(url('/usys/message', ['op' => 'login']));
        } catch (UserNotFoundException | UserActivityException $e) {
            return Xjs::response(
                $e->getMessage(),
                $e->getCode(),
                $tractor->getResponseData()
            );
        } catch (Throwable $e) {
            app('logger')->critical($e->getMessage());
            throw $e;
        }
    }

    /**
     * Logout
     */
    public function logout()
    {
        curuser()->logout();
    }
}
