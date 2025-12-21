<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Controller;

class AdminLoggerController extends Controller
{
    /**
     * Index
     */
    public function index()
    {
        return $this->view(null, (new AdminLoggerModel)->getIndexData());
    }

    /**
     * List
     */
    public function list()
    {
        return $this->view(null, (new AdminLoggerModel)->getListData());
    }

    /**
     * Show
     */
    public function show()
    {
        return $this->view(null, (new AdminLoggerModel)->getShowData());
    }

    /**
     * Status
     */
    public function status()
    {
        return $this->middleware('form.security')
            ?: $this->wrapper(fn() => (new LoggerModel)->status());
    }

    /**
     * Confirm delete
     */
    public function confirmDelete()
    {
        return $this->view(null, (new AdminLoggerModel)->getConfirmDeleteData());
    }

    /**
     * Delete
     */
    public function delete()
    {
        return $this->middleware('form.security')
            ?: $this->wrapper(fn() => (new LoggerModel)->delete());
    }

    /**
     * Confirm
     */
    public function confirmClean()
    {
        return $this->view();
    }

    /**
     * Clean
     */
    public function clean()
    {
        return $this->middleware('form.security')
            ?: $this->wrapper(fn() => (new LoggerModel)->clean());
    }

    /**
     * Confirm thin
     */
    public function confirmThin()
    {
        return $this->view();
    }

    /**
     * Thin
     */
    public function thin()
    {
        return $this->middleware('form.security')
            ?: $this->wrapper(fn() => (new LoggerModel)->thin());
    }

    /**
     * Confirm report
     */
    public function confirmReport()
    {
        return $this->view(null, (new AdminLoggerModel)->getConfirmReportData());
    }

    /**
     * Report
     */
    public function report()
    {
        return $this->middleware('form.security')
            ?: $this->wrapper(fn() => (new LoggerModel)->report());
    }
}
