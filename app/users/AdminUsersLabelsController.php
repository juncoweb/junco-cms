<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Controller;

class AdminUsersLabelsController extends Controller
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
        return $this->view(null, (new AdminUsersLabelsModel)->getListData());
    }

    /**
     * Create
     */
    public function create()
    {
        return $this->view('SaveForm', (new AdminUsersLabelsModel)->getCreateData());
    }

    /**
     * Edit
     */
    public function edit()
    {
        return $this->view('SaveForm', (new AdminUsersLabelsModel)->getEditData());
    }

    /**
     * Save
     */
    public function save()
    {
        return $this->middleware('form.security')
            ?: $this->wrapper(fn() => (new UsersLabelsModel)->save());
    }

    /**
     * Confirm delete
     */
    public function confirmDelete()
    {
        return $this->view(null, (new AdminUsersLabelsModel)->getConfirmDeleteData());
    }

    /**
     * Delete
     */
    public function delete()
    {
        return $this->middleware('form.security')
            ?: $this->wrapper(fn() => (new UsersLabelsModel)->delete());
    }
}
