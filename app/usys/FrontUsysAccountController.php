<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Controller;

class FrontUsysAccountController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->authenticate();
    }

    /**
     * Account
     */
    public function index()
    {
        return $this->view(null, (new FrontUsysAccountModel)->getIndexData());
    }

    /**
     * Update
     */
    public function update()
    {
        return $this->middleware('form.security')
            ?: $this->wrapper(fn() => (new UsysAccountModel)->update());
    }
}
