<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Controller;

class AdminExtensionsInstallerController extends Controller
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
        return $this->view(null, (new AdminExtensionsInstallerModel)->getListData());
    }

    /**
     * Confirm delete
     */
    public function confirmDelete()
    {
        return $this->view(null, (new AdminExtensionsInstallerModel)->getConfirmDeleteData());
    }

    /**
     * Delete
     */
    public function delete()
    {
        return $this->middleware('form.security', 'extensions.security')
            ?: $this->wrapper(fn() => (new ExtensionsInstallerModel)->delete());
    }

    /**
     * Confirm download
     */
    public function confirmDownload()
    {
        return $this->view(null, (new AdminExtensionsInstallerModel)->getConfirmDownloadData());
    }

    /**
     * Download
     */
    public function download()
    {
        return $this->middleware('form.security', 'extensions.security')
            ?: $this->wrapper(fn() => (new ExtensionsInstallerModel)->download());
    }

    /**
     * Confirm update
     */
    public function confirmUpdate()
    {
        return $this->view(null, (new AdminExtensionsInstallerModel)->getConfirmUpdateData());
    }

    /**
     * Update
     */
    public function update()
    {
        return $this->middleware('form.security', 'extensions.security')
            ?: $this->wrapper(fn() => (new ExtensionsInstallerModel)->update());
    }

    /**
     * Confirm upload
     */
    public function confirmUpload()
    {
        return $this->view();
    }

    /**
     * Upload
     */
    public function upload()
    {
        return $this->middleware('form.security', 'extensions.security')
            ?: $this->wrapper(fn() => (new ExtensionsInstallerModel)->upload());
    }

    /**
     * Confirm find updates
     */
    public function confirmFindUpdates()
    {
        return $this->view();
    }

    /**
     * Find updates
     */
    public function findUpdates()
    {
        return $this->middleware('form.security', 'extensions.security')
            ?: $this->wrapper(fn() => (new ExtensionsInstallerModel)->findUpdates());
    }

    /**
     * Confirm install
     */
    public function confirmInstall()
    {
        return $this->view(null, (new AdminExtensionsInstallerModel)->getConfirmInstallData());
    }

    /**
     * Install
     */
    public function install()
    {
        return $this->middleware('form.security', 'extensions.security')
            ?: $this->wrapper(fn() => (new ExtensionsInstallerModel)->install());
    }

    /**
     * Confirm unzip
     */
    public function confirmUnzip()
    {
        return $this->view(null, (new AdminExtensionsInstallerModel)->getConfirmUnzipData());
    }

    /**
     * Unzip
     */
    public function unzip()
    {
        return $this->middleware('form.security', 'extensions.security')
            ?: $this->wrapper(fn() => (new ExtensionsInstallerModel)->unzip());
    }

    /**
     * Confirm update
     */
    public function confirmUpdateAll()
    {
        return $this->view(null, (new AdminExtensionsInstallerModel)->getConfirmUpdateAllData());
    }

    /**
     * Update all
     */
    public function updateAll()
    {
        return $this->middleware('form.security', 'extensions.security')
            ?: $this->wrapper(fn() => (new ExtensionsInstallerModel)->updateAll());
    }

    /**
     * Confirm maintenance
     */
    public function confirmMaintenance()
    {
        return $this->view(null, (new AdminExtensionsInstallerModel)->getConfirmMaintenanceData());
    }

    /**
     * Maintenance
     */
    public function maintenance()
    {
        return $this->middleware('form.security')
            ?: $this->wrapper(fn() => (new ExtensionsInstallerModel)->maintenance());
    }

    /**
     * Show failure
     */
    public function showFailure()
    {
        return $this->view(null, (new AdminExtensionsInstallerModel)->getShowFailureData());
    }
}
