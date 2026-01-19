<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Controller;

class AdminCacheController extends Controller
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
        return $this->view(null, (new CacheModel)->getListData());
    }

    /**
     * Confirm delete
     */
    public function confirmDelete()
    {
        return $this->view(null, (new CacheModel)->getConfirmDeleteData());
    }

    /**
     * Delete
     */
    public function delete()
    {
        return $this->middleware('form.security')
            ?: $this->wrapper(fn() => (new CacheModel)->delete());
    }
}
