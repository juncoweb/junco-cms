<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Controller;

class AdminJobsFailuresController extends Controller
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
        return $this->view(null, (new AdminJobsFailuresModel)->getListData());
    }

    /**
     * Show
     */
    public function show()
    {
        return $this->view(null, (new AdminJobsFailuresModel)->getShowData());
    }

    /**
     * Status
     */
    public function status()
    {
        return $this->middleware('form.security')
            ?: $this->wrapper(fn() => (new JobsFailuresModel)->status());
    }

    /**
     * Confirm delete
     */
    public function confirmDelete()
    {
        return $this->view(null, (new AdminJobsFailuresModel)->getConfirmDeleteData());
    }

    /**
     * Delete
     */
    public function delete()
    {
        return $this->middleware('form.security')
            ?: $this->wrapper(fn() => (new JobsFailuresModel)->delete());
    }
}
