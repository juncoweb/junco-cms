<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Controller;

class FrontUsysActivationController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->authenticate(-1);
    }

    /**
     * Index
     */
    public function index()
    {
        return $this->view(null, (new FrontUsysActivationModel)->getIndexData());
    }

    /**
     * Reset
     */
    public function reset()
    {
        return $this->view(null, (new FrontUsysActivationModel)->getResetData());
    }

    /**
     * Send
     */
    public function sendToken()
    {
        return $this->middleware('form.security')
            ?: $this->wrapper(fn() => (new UsysActivationModel)->sendToken());
    }
}
