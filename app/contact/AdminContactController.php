<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Controller;

class AdminContactController extends Controller
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
        return $this->view(null, (new AdminContactModel)->getListData());
    }

    /**
     * Show
     */
    public function show()
    {
        return $this->view(null, (new AdminContactModel)->getShowData());
    }

    /**
     * Status
     */
    public function status()
    {
        return $this->middleware('form.security')
            ?: $this->wrapper(fn() => (new ContactModel)->status());
    }

    /**
     * Confirm delete
     */
    public function confirmDelete()
    {
        return $this->view(null, (new AdminContactModel)->getConfirmDeleteData());
    }

    /**
     * Delete
     */
    public function delete()
    {
        return $this->middleware('form.security')
            ?: $this->wrapper(fn() => (new ContactModel)->delete());
    }
}
