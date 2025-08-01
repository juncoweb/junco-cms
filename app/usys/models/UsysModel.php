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
    protected $db;
    protected string $verified = '';
    protected bool   $legal    = false;

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
            'fullname' => '',
            'username' => 'text',
            'email'    => 'email',
            'password' => 'required',
            'verified' => 'text',
            'legal'    => 'bool',
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
            return $this->unprocessable($e->getMessage());
        } catch (Throwable $e) {
            app('logger')->critical($e->getMessage());
            throw $e;
        }

        if ($result) {
            $token->destroy();
            curuser()->login($user_id);
            return $this->result()->reloadPage();
        }

        return $this->result()->redirectTo(url('/usys/message', ['op' => 'signup']));
    }

    /**
     * Login
     */
    public function login()
    {
        // data
        $this->filter(POST, [
            'email_username' => 'text',
            'password'       => '',
            'not_expire'     => 'bool',
            'redirect'       => 'text'
        ]);

        try {
            $tractor = new LoginTractor;
            $tractor->validateCredencial(
                $this->data['email_username'],
                $this->data['password'] ?? ''
            );

            $mfa_url = $this->getNextUrl($tractor->getUserId(), $this->data['redirect']);

            if ($mfa_url) {
                $tractor->preLogin($this->data['not_expire']);

                return $this->result()->redirectTo($mfa_url);
            }

            $tractor->login($this->data['not_expire']);

            if ($this->data['redirect'] == -1) {
                return $this->result()->goBack();
            }

            if ($this->data['redirect']) {
                return $this->result()->redirectTo($this->data['redirect']);
            }

            return $this->result()->reloadPage();
        } catch (UserNotActiveException $e) {
            return $this->result()->redirectTo(url('/usys/message', ['op' => 'login']));
        } catch (UserNotFoundException | UserActivityException $e) {
            return $this->unprocessable($e->getMessage())->setData($tractor->getResponseData());
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

    /**
     * Get
     */
    protected function getNextUrl(int $user_id, string $redirect): string
    {
        $plugins = config('usys.mfa_plugins');

        foreach ($plugins as $plugin) {
            $url = Plugin::get('mfa', 'status', $plugin)?->run($user_id, $redirect);

            if ($url) {
                return $url;
            }
        }

        return '';
    }
}
