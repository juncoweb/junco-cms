<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Model;
use Junco\Users\Enum\ActivityType;
use Junco\Users\Exception\UserActivityException;
use Junco\Users\Exception\UserNotActiveException;
use Junco\Users\Exception\UserNotFoundException;
use Junco\Users\Exception\UserValidationException;
use Junco\Users\Service\Login;
use Junco\Users\Service\Signup;
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
        $data = $this->filter(POST, [
            'token'    => 'text',
            'fullname' => 'text',
            'username' => 'text',
            'email'    => 'email',
            'password' => 'required',
            'verified' => 'text',
            'legal'    => 'bool',
        ]);

        //
        try {
            $user_id = 0;
            $token   = null;

            if ($data['token']) {
                $token = UserActivityToken::from($data['token'], ActivityType::signup);

                if ($token) {
                    $user_id = $token->getUserId();
                }
            }

            $service = new Signup();
            $result  = $service->signup(
                $data['fullname'],
                $data['username'],
                $data['email'],
                $data['password'],
                $data['verified'],
                $data['legal'],
                $user_id
            );
        } catch (UserValidationException $e) {
            return $this->unprocessable($e->getMessage());
        } catch (Throwable $e) {
            app('logger')->critical($e->getMessage());
            throw $e;
        }

        if ($result) {
            $token?->destroy();
            auth()->setDeferredLogin($user_id);
            return $this->result()->redirectTo(url('/usys/resolve'));
        }

        return $this->result()->redirectTo(url('/usys/message', ['op' => 'signup']));
    }

    /**
     * Login
     */
    public function login()
    {
        $data = $this->filter(POST, [
            'email_username' => 'text',
            'password'       => '',
            'remember'       => 'bool',
            'redirect'       => 'text'
        ]);

        try {
            $service = new Login;
            $service->validateCredencial(
                $data['email_username'],
                $data['password'] ?? ''
            );

            $mfa_url = $this->getNextUrl($service->getUserId(), $data['redirect']);

            if ($mfa_url) {
                $service->setDeferredLogin($data['remember']);

                return $this->result()->redirectTo($mfa_url);
            }

            $service->login($data['remember']);

            if ($data['redirect'] == -1) {
                return $this->result()->goBack();
            }

            if ($data['redirect']) {
                return $this->result()->redirectTo($data['redirect']);
            }

            return $this->result()->reloadPage();
        } catch (UserNotActiveException $e) {
            return $this->result()->redirectTo(url('/usys/message', ['op' => 'login']));
        } catch (UserNotFoundException | UserActivityException $e) {
            return $this->unprocessable($e->getMessage())->setData($service->getResponseData());
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
        auth()->logout();
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
