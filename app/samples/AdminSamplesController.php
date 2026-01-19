<?php

/**
 * @copyright (c) 2009-2026 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Controller;

class AdminSamplesController extends Controller
{
    /**
     * Edit
     */
    public function edit()
    {
        return $this->view(null, (new SamplesModel)->getEditData());
    }

    /**
     * Update
     */
    public function update()
    {
        return $this->middleware('form.security')
            ?: $this->wrapper(fn() => (new SamplesModel)->update());
    }

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
        return $this->view(null, (new SamplesModel)->getListData());
    }

    /**
     * Show
     */
    public function show()
    {
        return (new SamplesModel)->getShowData();
    }
}
