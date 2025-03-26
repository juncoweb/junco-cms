<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Controller;

class AdminUsersRolesController extends Controller
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
        return $this->view(null, (new AdminUsersRolesModel)->getListData());
    }

    /**
     * Create
     */
    public function create()
    {
        return $this->view('SaveForm', (new AdminUsersRolesModel)->getCreateData());
    }

    /**
     * Edit
     */
    public function edit()
    {
        return $this->view('SaveForm', (new AdminUsersRolesModel)->getEditData());
    }

    /**
     * Save
     */
    public function save()
    {
        return $this->middleware('form.security')
            ?: $this->wrapper(fn() => (new UsersRolesModel)->save());
    }

    /**
     * Confirm delete
     */
    public function confirmDelete()
    {
        return $this->view(null, (new AdminUsersRolesModel)->getConfirmDeleteData());
    }

    /**
     * Delete
     */
    public function delete()
    {
        return $this->middleware('form.security')
            ?: $this->wrapper(fn() => (new UsersRolesModel)->delete());
    }
}
