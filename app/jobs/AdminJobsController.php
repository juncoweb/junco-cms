<?php

/**
 * @copyright (c) 2009-2025 by Junco CMS
 * @author: Junco CMS (tm)
 */

use Junco\Mvc\Controller;

class AdminJobsController extends Controller
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
        return $this->view(null, (new AdminJobsModel)->getListData());
    }

    /**
     * Show
     */
    public function show()
    {
        return $this->view(null, (new AdminJobsModel)->getShowData());
    }
}
