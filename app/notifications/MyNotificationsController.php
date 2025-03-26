<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Controller;

class MyNotificationsController extends Controller
{
    /**
     * Index
     */
    public function index()
    {
        return $this->view();
    }

    /**
     * List
     */
    public function list()
    {
        return $this->view(null, (new MyNotificationsModel)->getListData());
    }

    /**
     * Show
     */
    public function show()
    {
        return $this->view(null, (new MyNotificationsModel)->getShowData());
    }

    /**
     * Status
     */
    public function status()
    {
        return $this->middleware('form.security')
            ?: $this->wrapper(fn() => (new NotificationsModel)->status());
    }
}
