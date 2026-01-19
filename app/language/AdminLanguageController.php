<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Controller;

class AdminLanguageController extends Controller
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
        return $this->view(null, (new AdminLanguageModel)->getListData());
    }

    /**
     * Edit
     */
    public function edit()
    {
        return $this->view('SaveForm', (new AdminLanguageModel)->getEditData());
    }

    /**
     * Save
     */
    public function save()
    {
        return $this->middleware('form.security')
            ?: $this->wrapper(fn() => (new LanguageModel)->save());
    }

    /**
     * Confirm duplicate
     */
    public function confirmDuplicate()
    {
        return $this->view(null, (new AdminLanguageModel)->getConfirmDuplicateData());
    }

    /**
     * Duplicate
     */
    public function duplicate()
    {
        return $this->middleware('form.security')
            ?: $this->wrapper(fn() => (new LanguageModel)->duplicate());
    }

    /**
     * Confirm select
     */
    public function confirmSelect()
    {
        return $this->view(null, (new AdminLanguageModel)->getConfirmSelectData());
    }

    /**
     * Select
     */
    public function select()
    {
        return $this->middleware('form.security')
            ?: $this->wrapper(fn() => (new LanguageModel)->select());
    }

    /**
     * Status
     */
    public function status()
    {
        return $this->middleware('form.security')
            ?: $this->wrapper(fn() => (new LanguageModel)->status());
    }

    /**
     * Confirm delete
     */
    public function confirmDelete()
    {
        return $this->view(null, (new AdminLanguageModel)->getConfirmDeleteData());
    }

    /**
     * Delete
     */
    public function delete()
    {
        return $this->middleware('form.security')
            ?: $this->wrapper(fn() => (new LanguageModel)->delete());
    }

    /**
     * Export
     */
    public function export()
    {
        return $this->view(null, (new LanguageModel)->export());
    }

    /**
     * Confirm import
     */
    public function confirmImport()
    {
        return $this->view();
    }

    /**
     * Import
     */
    public function import()
    {
        return $this->middleware('form.security')
            ?: $this->wrapper(fn() => (new LanguageModel)->import());
    }

    /**
     * Confirm refresh
     */
    public function confirmRefresh()
    {
        return $this->view();
    }

    /**
     * Refresh
     */
    public function refresh()
    {
        return $this->middleware('form.security')
            ?: $this->wrapper(fn() => (new LanguageHelper)->refresh());
    }

    /**
     * Confirm distribute
     */
    public function confirmDistribute()
    {
        return $this->view(null, (new AdminLanguageModel)->getConfirmDistributeData());
    }

    /**
     * Distribute
     */
    public function distribute()
    {
        return $this->middleware('form.security')
            ?: $this->wrapper(fn() => (new LanguageModel)->distribute());
    }
}
