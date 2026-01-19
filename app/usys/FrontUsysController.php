<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Controller;

class FrontUsysController extends Controller
{
    /**
     * Signup
     */
    public function signup()
    {
        curuser()->getId() and redirect();

        return $this->view(null, (new FrontUsysModel)->getSignupData());
    }

    /**
     * Take signup
     */
    public function takeSignup()
    {
        return $this->middleware('form.security')
            ?: $this->authenticate(-1)
            ?: $this->wrapper(fn() => (new UsysModel)->signup());
    }

    /**
     * Login
     */
    public function resolve()
    {
        curuser()->getId() and redirect();

        return (new FrontUsysModel)->getResolveData();
    }

    /**
     * Login
     */
    public function login()
    {
        curuser()->getId() and redirect();

        return $this->view(null, (new FrontUsysModel)->getLoginData());
    }

    /**
     * Take Login
     */
    public function takeLogin()
    {
        return $this->middleware('form.security')
            ?: $this->wrapper(fn() => (new UsysModel)->login());
    }

    /**
     * Logout
     */
    public function logout()
    {
        return $this->view();
    }

    /**
     * Take logout
     */
    public function takeLogout()
    {
        return $this->middleware('form.security')
            ?: $this->wrapper(fn() => (new UsysModel)->logout());
    }

    /**
     * Autologin
     */
    public function autologin()
    {
        return $this->authenticate(-1)
            ?: $this->view(null, (new FrontUsysModel)->getAutologinData());
    }

    /**
     * Message
     */
    public function message()
    {
        return $this->view(null, (new FrontUsysModel)->getMessageData());
    }
}
