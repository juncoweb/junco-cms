<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Controller;

class AdminExtensionsController extends Controller
{
    /**
     * Index
     */
    public function index()
    {
        return $this->view(null, (new AdminExtensionsModel)->getIndexData());
    }

    /**
     * List
     */
    public function list()
    {
        return $this->view(null, (new AdminExtensionsModel)->getListData());
    }

    /**
     * Create
     */
    public function create()
    {
        return $this->view('SaveForm', (new AdminExtensionsModel)->getCreateData());
    }

    /**
     * Edit
     */
    public function edit()
    {
        return $this->view('SaveForm', (new AdminExtensionsModel)->getEditData());
    }

    /**
     * Save
     */
    public function save()
    {
        return $this->middleware('form.security')
            ?: $this->wrapper(fn() => (new ExtensionsModel)->save());
    }

    /**
     * Confirm status
     */
    public function confirmStatus()
    {
        return $this->view(null, (new AdminExtensionsModel)->getConfirmStatusData());
    }

    /**
     * Status
     */
    public function status()
    {
        return $this->middleware('form.security')
            ?: $this->wrapper(fn() => (new ExtensionsModel)->status());
    }

    /**
     * Confirm delete
     */
    public function confirmDelete()
    {
        return $this->view(null, (new AdminExtensionsModel)->getConfirmDeleteData());
    }

    /**
     * Delete
     */
    public function delete()
    {
        return $this->middleware('form.security')
            ?: $this->wrapper(fn() => (new ExtensionsModel)->delete());
    }

    /**
     * Confirm append
     */
    public function confirmAppend()
    {
        return $this->view(null, (new AdminExtensionsModel)->getConfirmAppendData());
    }

    /**
     * Append
     */
    public function append()
    {
        return $this->middleware('form.security')
            ?: $this->wrapper(fn() => (new ExtensionsModel)->append());
    }

    /**
     * Confirm compile
     */
    public function confirmCompile()
    {
        return $this->view(null, (new AdminExtensionsModel)->getConfirmCompileData());
    }

    /**
     * Compile
     */
    public function compile()
    {
        return $this->middleware('form.security')
            ?: $this->wrapper(fn() => (new ExtensionsModel)->compile());
    }

    /**
     * DB History Form
     */
    public function confirmDbhistory()
    {
        return $this->view(null, (new AdminExtensionsModel)->getConfirmDbHistoryData());
    }

    /**
     * DB History
     */
    public function dbHistory()
    {
        return $this->middleware('form.security')
            ?: $this->wrapper(fn() => (new ExtensionsModel)->dbHistory());
    }

    /**
     * Edit
     */
    public function editReadme()
    {
        return $this->view(null, (new AdminExtensionsModel)->getEditReadmeData());
    }

    /**
     * Update
     */
    public function updateReadme()
    {
        return $this->middleware('form.security')
            ?: $this->wrapper(fn() => (new ExtensionsModel)->updateReadme());
    }

    /**
     * Distribute
     */
    public function distribute()
    {
        return $this->view(null, (new ExtensionsModel)->distribute());
    }
}
