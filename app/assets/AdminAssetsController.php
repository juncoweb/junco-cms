<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Controller;

class AdminAssetsController extends Controller
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
        return $this->view(null, (new AdminAssetsModel)->getListData());
    }

    /**
     * Create
     */
    public function create()
    {
        return $this->view('SaveForm', (new AdminAssetsModel)->getCreateData());
    }

    /**
     * Edit
     */
    public function edit()
    {
        return $this->view('SaveForm', (new AdminAssetsModel)->getEditData());
    }

    /**
     * Save
     */
    public function save()
    {
        return $this->middleware('form.security')
            ?: $this->wrapper(fn() => (new AssetsModel)->save());
    }

    /**
     * Confirm delete
     */
    public function confirmDelete()
    {
        return $this->view(null, (new AdminAssetsModel)->getConfirmDeleteData());
    }

    /**
     * Delete
     */
    public function delete()
    {
        return $this->middleware('form.security', 'extensions.security')
            ?: $this->wrapper(fn() => (new AssetsModel)->delete());
    }

    /**
     * Confirm compile
     */
    public function confirmCompile()
    {
        return $this->view(null, (new AdminAssetsModel)->getConfirmCompileData());
    }

    /**
     * Compile
     */
    public function compile()
    {
        return $this->middleware('form.security')
            ?: $this->wrapper(fn() => (new AssetsModel)->compile());
    }

    /**
     * Inspect
     */
    public function inspect()
    {
        return $this->middleware('form.security')
            ?: $this->wrapper(fn() => (new AssetsModel)->inspectAll());
    }

    /**
     * Options
     */
    public function confirmOptions()
    {
        return $this->view(null, (new AdminAssetsModel)->getConfirmOptionsData());
    }

    /**
     * Options
     */
    public function options()
    {
        return $this->middleware('form.security')
            ?: $this->wrapper(fn() => (new AssetsModel)->options());
    }
}
