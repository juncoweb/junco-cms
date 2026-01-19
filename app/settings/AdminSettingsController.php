<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Controller;

class AdminSettingsController extends Controller
{
    /**
     * Index
     */
    public function index()
    {
        if (router()->isFormat('text')) {
            return $this->form();
        }

        return $this->view(null, (new AdminSettingsModel)->getIndexData());
    }

    /**
     * Form
     */
    public function form()
    {
        return $this->view(null, (new AdminSettingsFormModel)->getFormData());
    }

    /**
     * Json
     */
    public function json()
    {
        return $this->view(null, (new AdminSettingsModel)->getJsonData());
    }

    /**
     * Edit
     */
    public function edit()
    {
        return $this->view(null, (new AdminSettingsModel)->getEditData());
    }

    /**
     * Update
     */
    public function update()
    {
        return $this->middleware('form.security', 'extensions.security')
            ?: $this->wrapper(fn() => (new SettingsModel)->save());
    }

    /**
     * Confirm delete
     */
    public function confirmDelete()
    {
        return $this->view(null, (new AdminSettingsModel)->getConfirmDeleteData());
    }

    /**
     * Delete
     */
    public function delete()
    {
        return $this->middleware('form.security', 'extensions.security')
            ?: $this->wrapper(fn() => (new SettingsModel)->delete());
    }

    /**
     * Prepare
     */
    public function prepare()
    {
        return $this->view(null, (new AdminSettingsModel)->getPrepareData());
    }

    /**
     * Take
     */
    public function take()
    {
        return $this->middleware('form.security', 'extensions.security')
            ?: $this->wrapper(fn() => (new SettingsModel)->set());
    }
}
