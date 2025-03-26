<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Controller;

class AdminAssetsVariablesController extends Controller
{
    /**
     * Index
     */
    public function index()
    {
        return $this->view(null, (new AdminAssetsVariablesModel)->getIndexData());
    }

    /**
     * List
     */
    public function list()
    {
        return $this->view(null, (new AdminAssetsVariablesModel)->getListData());
    }

    /**
     * Edit
     */
    public function edit()
    {
        return $this->view(null, (new AdminAssetsVariablesModel)->getEditData());
    }

    /**
     * Update
     */
    public function update()
    {
        return $this->middleware('form.security')
            ?: $this->wrapper(fn() => (new AdminAssetsVariablesModel)->update());
    }
}
